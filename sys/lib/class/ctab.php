<?php    
    function createTab($tab)
    {
        $cls[$tab['curr']] = "curr";
        $tabs = $tab['tab'];
        $html = "";
        for ($i=0; $i<count($tabs); $i++)
        {
            $name = $tabs[$i][0];
            $url  = $tabs[$i][1];
            $str  = $tabs[$i][2];
            
            if ($tab['type']==2 && $i>0) $html .= "|";
            $html .= "
						<div id=tab_{$name} onclick='window.location.href=\"$url\"' onmouseover='this.className=\"hover\"' onmouseout='this.className=\"item $cls[$name]\"' class='item $cls[$name]'>
							$str
						</div>";
        }
        
        echo "
                <div class=tabBox{$tab['type']}>
                    <div class=title>
                        {$tab['title']} <span class=tool>{$tab['tool']}</span>
                    </div>
                    <div class=tab>
                        $html
                    </div>
                    <div class=clear></div>
                </div>
             ";
    }
    
    function createTab2($tab)
    {
        $cls[$tab['curr']] = "curr";
        $tabs = $tab['tab'];
        $html = "";
        for ($i=0; $i<count($tabs); $i++)
        {
            $name = $tabs[$i][0];
            $url  = $tabs[$i][1];
            $str  = $tabs[$i][2];
            /*
            $html .= "<div onclick='window.location.href=\"$url\"' onmouseover='this.className=\"hover\"' onmouseout='this.className=\"item $cls[$name]\"' class='item $cls[$name]'>
						$str
					  </div>";
			*/
			$class = (array_key_exists($name, $cls)) ? $cls[$name] : "item";
			$html .= "<div onclick='window.location.href=\"$url\"' class='$class'>
						<div class='page" .($i+1). "'>
						$str
						</div>
					  </div>";			
        }
        
        return "<div class=tabBox id=tabBox>
				<div class=tabBox2>
				<div class=tabBox3>
					<div class=tabHeader>
						<div class=title>
							{$tab['title']} <span class=tool>{$tab['tool']}</span>
						</div>
						<div class=tab>
							$html
						</div>
						<div class=clear></div>
					</div>
					<div class=tabBody>
						<div class=tabBody2>
							<div class=tabBody3>
								{$tab['content']}
							</div>
						</div>
					</div>
					<div class=tabFooter></div>
                </div>
				</div>
				</div>
               ";
    }
?>