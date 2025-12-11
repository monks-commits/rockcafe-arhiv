<img src="../img/title-news.gif" alt="Новости &quot;Рок-Кафе&quot;" width=200 height=60 hspace=0 vspace=0 align=left>
<? 
$t_line = "<td align=right class=menu1>[ <a href='#top'>наверх</a> ]</td>";
require("../guests.php"); 
?>

<br clear=all>
<hr size=2 color=#ffffff>

<!-- ##################################################### -->

<p>

<?php
$itemNum=0;
class RSSParser	{
	var $channel_title="";
	var $channel_website="";
	var $channel_description="";
	var $channel_pubDate="";
	var $channel_lastUpdated="";
	var $channel_copyright="";
	var $title="";
	var $link="";
	var $description="";
	var $pubDate="";
	var $author="";
	var $url="";
	var $width="";
	var $height="";
	var $inside_tag=false;	
	function RSSParser($file)	{
			$this->xml_parser = xml_parser_create();
			xml_set_object( $this->xml_parser, &$this );
			xml_set_element_handler( $this->xml_parser, "startElement", "endElement" );
			xml_set_character_data_handler( $this->xml_parser, "characterData" );
			$fp = @fopen("$file","r") or die( "$file could not be opened" );
			while ($data = fread($fp, 4096)){xml_parse( $this->xml_parser, $data, feof($fp)) or die( "XML error");}
			fclose($fp);
			xml_parser_free( $this->xml_parser );
		}
	
	function startElement($parser,$tag,$attributes=''){
		$this->current_tag=$tag;
		if($this->current_tag=="ITEM" || $this->current_tag=="IMAGE"){
			$this->inside_tag=true;
			$this->description="";
			$this->link="";
			$this->title="";
			$this->pubDate="";
		}
	}
	
	function endElement($parser, $tag){
		switch($tag){
			case "ITEM":
				$this->titles[]=trim($this->title);
				$this->links[]=trim($this->link);
				$this->descriptions[]=trim($this->description);
				$this->pubDates[]=trim($this->pubDate);
				$this->authors[]=trim($this->author);
				$this->author=""; $this->inside_tag=false;
				break;
			case "IMAGE":
				$this->channel_image="<img src=\"".trim($this->url)."\" width=\"".trim($this->width)."\" height=\"".trim($this->height)."\" alt=\"".trim($this->title)."\" border=\"0\" title=\"".trim($this->title)."\" />";
				$this->title=""; $this->inside_tag=false;
			default:
				break;
		}
	}
	
	function characterData($parser,$data){
		if($this->inside_tag){
			switch($this->current_tag){
				case "TITLE":
					$this->title.=$data; break;
				case "DESCRIPTION":
					$this->description.=$data; break;
				case "LINK":
					$this->link.=$data; break;
				case "URL":
					$this->url.=$data; break;					
				case "WIDTH":
					$this->width.=$data; break;
				case "HEIGHT":
					$this->height.=$data; break;
				case "PUBDATE":
					$this->pubDate.=$data; break;
				case "AUTHOR":
					$this->author.=$data;	break;
				default: break;									
			}//end switch
		}else{
			switch($this->current_tag){
				case "DESCRIPTION":
					$this->channel_description.=$data; break;
				case "TITLE":
					$this->channel_title.=$data; break;
				case "LINK":
					$this->channel_website.=$data; break;
				case "COPYRIGHT":
					$this->channel_copyright.=$data; break;
				case "PUBDATE":
					$this->channel_pubDate.=$data; break;					
				case "LASTBUILDDATE":
					$this->channel_lastUpdated.=$data; break;				
				default:
					break;
			}
		}
	}
}

$csablog = new RSSParser("http://seva.ru/rss/");
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="a">Channel Title: <strong><?php echo $csablog->channel_title; ?></strong></td>
  </tr>
  <tr>
    <td class="a">Channel Website: <strong><?php echo $csablog->channel_website; ?></strong></td>
  </tr>
  <tr>
    <td class="a"><p>Channel Description: <strong><?php echo $csablog->channel_description; ?></strong></td>
  </tr>
  <tr>
    <td class="a"><p>Channel Copyright: <strong><?php echo $csablog->channel_copyright; ?></strong></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><?php
$csablog_RSSmax=20;
if($csablog_RSSmax==0 || $csablog_RSSmax>count($csablog->titles))$csablog_RSSmax=count($csablog->titles);
for($itemNum=0;$itemNum<$csablog_RSSmax;$itemNum++){?>
      <table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="a"><!--Item:--><strong><?php echo $csablog->titles[$itemNum]; ?></strong></td>
        </tr>
        <tr>
          <td class="a"><!--Description:--><?php echo $csablog->descriptions[$itemNum]; ?></td>
        </tr>
        <tr>
                <td class="a" align="right"><a href="<?php echo $csablog->links[$itemNum]; ?>" target="_blank">...read 
                  more</a> <br>
                  <br>
		  </td>
        </tr>
          </table>
      <?php } ?></td>
  </tr>
  <tr>
    <td><a href="<?php echo $csablog->channel_website; ?>" target="_blank">Channel Link</a> </td>
  </tr>
  <tr>
    <td><br><?php echo $csablog->channel_image; ?></td>
  </tr>
</table>


<br clear=all><br>

<hr size=2 color=#ffffff>

<table width=100% cellspacing=0 cellpadding=0 border=0 bgcolor=#933c93>
<tr>
<td width=100% class=menu1><? require("../inc/banner_link_hor.php"); ?></td>
<td class=menu1 align=absmiddle>[&nbsp;<a href='#top'>наверх</a>&nbsp;]</td>
</tr>
</table>

<br><br><br><br>
