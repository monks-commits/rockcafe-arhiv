#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Static exporter for legacy Rock-Cafe PHP site to GitHub Pages.

What it does:
- Creates *.html versions of known *.php pages by inlining corresponding *-content.php.
- Removes all remaining PHP blocks (<? ... ?>).
- Rewrites internal links: *.php -> *.html (keeps external http(s) as-is).
- Fixes landing index.html (eagle) to point to index1.html (or other target).
"""

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parent

# Pages that usually consist of a "layout.php" + "content.php"
PAGE_MAP = [
    ("about", "about.php", "about-content.php", "about.html"),
    ("afisha", "afisha.php", "afisha-content.php", "afisha.html"),
    ("coord", "coord.php", "coord-content.php", "coord.html"),
    ("dates", "dates.php", "dates-content.php", "dates.html"),
    ("links", "links.php", "links-content.php", "links.html"),
    ("menu1", "menu1.php", "menu1-content.php", "menu1.html"),
    ("menu2", "menu2.php", "menu2-content.php", "menu2.html"),
    ("mero", "mero.php", "mero-content.php", "mero.html"),
    ("photo", "photo.php", "photo-content.php", "photo.html"),
    ("search", "search.php", "search-content.php", "search.html"),
    ("search_result", "search_result.php", "search_result-content.php", "search_result.html"),
    ("callback", "callback.php", "callback-content.php", "callback.html"),
    ("callback_form", "callback-form.php", None, "callback-form.html"),  # may be self-contained
    ("guests", "guests.php", None, "guests.html"),                      # may be included only
    ("line", "line.php", None, "line.html"),                            # may be included only
]

# Your "main menu" page:
# In your repo it's currently index1.php renamed to index_work.php,
# and you already have index1.html working.
# We'll also generate index_work.html if index_work.php exists.
EXTRA_PAGES = [
    ("index_work", "index_work.php", None, "index_work.html"),
]

# Landing (eagle) file. This must exist and should be pure HTML.
LANDING_HTML = "index.html"
LANDING_TARGET = "index1.html"   # where the button should lead

# --- Helpers ---

PHP_BLOCK_RE = re.compile(r"<\?(?:php)?[\s\S]*?\?>", re.IGNORECASE)

# rewrite internal links to .html
def rewrite_links(html: str) -> str:
    # Replace href="something.php" -> href="something.html"
    # but keep absolute http(s) links intact.
    def repl_href(m):
        url = m.group(1)
        if url.startswith("http://") or url.startswith("https://"):
            return m.group(0)
        # keep anchors
        url2 = re.sub(r"\.php(\b|#)", r".html\1", url)
        return f'href="{url2}"'

    html = re.sub(r'href="([^"]+)"', repl_href, html, flags=re.IGNORECASE)

    # Replace src="something.php" rarely used, but just in case
    def repl_src(m):
        url = m.group(1)
        if url.startswith("http://") or url.startswith("https://"):
            return m.group(0)
        url2 = re.sub(r"\.php(\b|#)", r".html\1", url)
        return f'src="{url2}"'

    html = re.sub(r'src="([^"]+)"', repl_src, html, flags=re.IGNORECASE)
    return html

def strip_php(html: str) -> str:
    return PHP_BLOCK_RE.sub("", html)

def read_text(path: Path) -> str:
    # We try to read as cp1251 first, then utf-8
    data = path.read_bytes()
    for enc in ("cp1251", "windows-1251", "utf-8"):
        try:
            return data.decode(enc)
        except UnicodeDecodeError:
            continue
    # fallback: latin1
    return data.decode("latin1")

def write_text(path: Path, text: str) -> None:
    # keep legacy encoding as cp1251 to preserve original bytes best
    path.write_bytes(text.encode("cp1251", errors="replace"))

def inline_content(layout_html: str, content_html: str | None) -> str:
    if not content_html:
        return layout_html

    # 1) Try to replace the exact PHP include logic block (common in your index_work.php)
    #    <? if (!$inc) require("about-content.php"); else require($inc); ?>
    # We'll just replace the whole <?...?> with the content.
    out = re.sub(r"<\?(?:php)?[\s\S]*?require\((?:\"|')?[^\"')]+(?:\"|')?\)[\s\S]*?\?>",
                 content_html,
                 layout_html,
                 flags=re.IGNORECASE)

    # 2) If nothing changed, also try to replace a placeholder comment (if you used it)
    if out == layout_html:
        out = layout_html.replace("<!-- CONTENT_INJECT -->", content_html)

    return out

def export_page(layout_file: Path, content_file: Path | None, out_file: Path) -> bool:
    if not layout_file.exists():
        return False

    layout = read_text(layout_file)

    content = None
    if content_file and content_file.exists():
        content = read_text(content_file)
        # content files often contain PHP requires: remove them
        content = strip_php(content)

    merged = inline_content(layout, content)

    # final cleanup
    merged = strip_php(merged)
    merged = rewrite_links(merged)

    # normalize: avoid leading BOM weirdness
    merged = merged.replace("\ufeff", "")

    write_text(out_file, merged)
    return True

def fix_landing() -> None:
    landing = ROOT / LANDING_HTML
    if not landing.exists():
        print(f"[WARN] {LANDING_HTML} not found, skipping landing fix.")
        return

    html = read_text(landing)
    # Replace any href to index_work.php or index1.php or *.php in the main button to LANDING_TARGET
    # We look for: <a href="...">Войти на сайт</a> or similar
    html2 = re.sub(r'(class="btn-enter"\s+href=")[^"]+(")',
                   rf'\1{LANDING_TARGET}\2',
                   html,
                   flags=re.IGNORECASE)

    # If no btn-enter class exists, fallback: replace first occurrence of href="index_work.php"/"index1.php"
    if html2 == html:
        html2 = html.replace('href="index_work.php"', f'href="{LANDING_TARGET}"') \
                    .replace('href="index1.php"', f'href="{LANDING_TARGET}"') \
                    .replace('href="index.php"', f'href="{LANDING_TARGET}"')

    html2 = rewrite_links(html2)
    write_text(landing, html2)
    print(f"[OK] landing fixed: {LANDING_HTML} -> {LANDING_TARGET}")

def main():
    created = []

    for name, layout, content, out in PAGE_MAP:
        layout_path = ROOT / layout
        content_path = (ROOT / content) if content else None
        out_path = ROOT / out
        ok = export_page(layout_path, content_path, out_path)
        if ok:
            created.append(out)
            print(f"[OK] {layout} + {content or '(no content)'} -> {out}")
        else:
            print(f"[SKIP] missing {layout}")

    for name, layout, content, out in EXTRA_PAGES:
        layout_path = ROOT / layout
        out_path = ROOT / out
        ok = export_page(layout_path, None, out_path)
        if ok:
            created.append(out)
            print(f"[OK] {layout} -> {out}")
        else:
            print(f"[SKIP] missing {layout}")

    # Also fix internal links in existing index1.html (if you already have it)
    idx1 = ROOT / "index1.html"
    if idx1.exists():
        html = read_text(idx1)
        html2 = rewrite_links(html)
        write_text(idx1, html2)
        print("[OK] patched links in index1.html (*.php -> *.html)")

    fix_landing()

    print("\nDONE. Created/updated:")
    for f in created:
        print(" -", f)
    print("\nNext: commit + push, then hard refresh your site (Ctrl+F5).")

if __name__ == "__main__":
    main()
