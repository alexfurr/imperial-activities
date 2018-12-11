<?php

$imperialIcons = new imperialIcons();

class imperialIcons
{

	//~~~~~
	function __construct ()
	{
		
		$this->addWPActions();		
	}
	

	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
		add_shortcode( 'imperial-icon', array( $this, 'drawIcon' ) );

		

	}
	
	
	
    function drawIcon($atts)
	{
		$atts = shortcode_atts( 
			array(
				'icon'		=> '',
				'size'		=> 's',
				'background'	=> true,
				'colour'		=> "black",
				'bg-colour' 	=> "#ccc",
				), 
			$atts
		);
		
		$str='';
		
		$icon = $atts['icon'];
		$size = $atts['size'];
		$background = $atts['background'];
		$colour = $atts['colour'];
		$bg_colour = $atts['bg-colour'];
		
		
		switch ($size)
		{

			

			
			case "l":
			
				$iconSize = "2";
				$circleSize  = "2";
				$backgroundSize = "4";	

				if($background==false)
				{
					$iconSize = 4;
				}
			break;
			
			
			case "m":
			
				$iconSize = "1";
				$circleSize  = "2";
				$backgroundSize = "2";			
				
				if($background==false)
				{
					$iconSize = 2;
				}			
				
			
			break;		
			
			case "s":
			default:
			
				$iconSize = "1";
				$circleSize  = "2";
				$backgroundSize = "1";
				
				if($background==false)
				{
					$iconSize = 1;
				}				
				
			break;			
			
		}
		
		$html='';
		
		
		if($background==true)
		{		
			$html.='<div class="fa-stack fa-'.$backgroundSize.'x">';
			$html.='<i class="fa fa-circle fa-stack-'.$circleSize.'x" style="color:'.$bg_colour.'"></i>';
			
			
			
		}
		

		
		// The actual icon itself
		$html.='<i class="fa fa-'.$icon;
		if($background==false)
		{
			$html.=' fa-'.$iconSize.'x ';
		}
		
		
		
		if($background==true)
		{
			$html.=' fa-inverse fa-stack-'.$iconSize.'x ';
		}
		
		
		$html.=' " style="color:'.$colour.'"></i>';
		
		
		
		
		if($background==true)
		{		
					
		
		$html.='</div>';
		
		}
		

		

		
		return $html;
	}	
	

}
?>