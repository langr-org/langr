<?php
#============================================================================================================================================================
# 名    稱: XML v 0.07.08
# 功    能: XML 讀取到資料處理
# 作    者：Arnold, arnold@addwe.com
# 使用舉例：
#	$xmlFile	= "http://210.64.24.47/videochat/php/public/roomList.php";
#	$r = new Xml2Array($xmlFile);
#	$r->parse();
#	$arrResult = $r->getResult();
#	$r->freeResult();
#
#	echo "<PRE>";
#	print_r($arrResult);
#	echo "</PRE>";
#------------------------------------------------------------------------------------------------------------------------------------------------------------
define(WRITE_FLAG_NOT_NULL,		 1);
define(WRITE_FLAG_NULL,				 2);
define(WRITE_FLAG_EMPTY_ARRAY, 4);

class	Tool_Xml2Array
{
	/**
	*	The	XML	file name( can be	also real	path and web url ).
	*/
	var	$xmlFile;
	/**
	*	The	array	result from	this class.
	*/
	var	$arrResult	=	array();
	/**
	*	The	key	stack, I keep	each tag name	of each	level	to stack array.
	*/
	var	$keyStack	=	array();
	/**
	*	Deep of	stack	array.
	*/
	var	$stackIndex	=	0;
	
	/**
	*	The	XML	Parser ..	
	*/
	var	$parser;

	/**
	*	The	constructor.
	*	@xmlFile (string)	-	XML	source file	name
	*/
	function Tool_Xml2Array($xmlFile)
	{
		$this->xmlFile	=	$xmlFile;
		$this->parser	=	xml_parser_create();
		xml_set_object($this->parser,	&$this);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING,	false);
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE,true);
		xml_set_element_handler($this->parser, "startElement", "endElement");
		xml_set_character_data_handler($this->parser,	"characterData");
	}

	/**
	*	start	element	handler
	*/
	function startElement($parser, $name,	$atts)
	{
		$name	=	trim($name);

		$txt = "\$arr	=	"	.	$this->getArrayName($this->stackIndex) . ";";
		eval($txt);

		$this->keyStack[$this->stackIndex] = count($arr);
		$this->stackIndex++;
		$this->writeData("", WRITE_FLAG_EMPTY_ARRAY);

		if(!empty($name))
		{
			// using the element name	to array key.. 
			$this->keyStack[$this->stackIndex] = "'name'";
			$this->stackIndex++;
			$this->writeData($name,	WRITE_FLAG_NULL);
			$this->stackIndex--;

			$this->keyStack[$this->stackIndex] = "'attr'";
			$this->stackIndex++;
			if(!empty($atts))
			{
				// using the attributes	to array key.. 
				foreach($atts	as $attKey =>	$attVal)
				{
					$this->keyStack[$this->stackIndex] = "'".$attKey."'";
					$this->stackIndex++;
					$this->writeData($attVal,	WRITE_FLAG_NULL);
					$this->stackIndex--;
				}
			}
			else
			{
				$this->writeData("", WRITE_FLAG_EMPTY_ARRAY);
			}
			$this->stackIndex--;

			$this->keyStack[$this->stackIndex] = "'cont'";
			$this->stackIndex++;
		}
	}

	/**
	*	end	element	handler
	*/
	function endElement($parser, $name)
	{
		$this->stackIndex-=2;
	}
	
	/**
	*	none tag element handler
	*/
	function characterData($parser,	$data)
	{
		$data	=	trim($data);
		if(!empty($data))
		{
			$txt = "\$arr	=	"	.	$this->getArrayName($this->stackIndex) . ";";
			eval($txt);

			$this->keyStack[$this->stackIndex] = count($arr);
			$this->stackIndex++;
			$this->writeData($data,	WRITE_FLAG_NOT_NULL);
			$this->stackIndex--;
		}
	}

	/**
	*	write	data in	array
	*/
	function writeData($data,	$flag)
	{
		$data	=	trim($data);
		$dataStr = "\""	.	$data	.	"\"";
		switch(	$flag	)
		{
			case WRITE_FLAG_NOT_NULL:
			default:
				// stop	if there isn't any data
				if(empty($data))
					break;
			case WRITE_FLAG_EMPTY_ARRAY:
				// overwrite default dataStr
				if(empty($data))
					$dataStr = "array()";
			case WRITE_FLAG_NULL:
				$txt = $this->getArrayName($this->stackIndex)."	=	".$dataStr.";";
				// assign	value	to the result	array	.. 
				eval($txt);

			break;
		}
	}

	/**
	*	get	name of	array
	*/
	function getArrayName($depth)
	{
		if($depth)
		{
			for($i=0;	$i<$depth; $i++)
			{
				$key .=	$this->keyStack[$i]."|";
			}
			$key = preg_replace("/\|$/", "]",	$key);
			$key = preg_replace("/\|/",	"][",	$key);
			return "\$this->arrResult[".$key;
		}
		else
		{
			return "\$this->arrResult";
		}
	}

	/**
	*	parsing	.. 
	*/
	function parse()
	{
		if (!($fp	=	fopen($this->xmlFile,	"r"))) 
		{
			die("Could not open	XML	input!");
		}		 

		while	($data = fread($fp,	4096))
		{
			if (!xml_parse($this->parser,	$data, feof($fp)))
			{
				die(sprintf("XML error:	%s at	line %d",	xml_error_string(xml_get_error_code($this->parser)),xml_get_current_line_number($this->parser)));
			}
		}
		fclose($fp);
	}

	/**
	*	@return	the	result array 
	*/
	function getResult()
	{
		return $this->arrResult;
	}

	/**
	*	free the XML parser	result ..	
	*/
	function freeResult()
	{
		xml_parser_free($this->parser);
	}
	
}	// end class	

?>