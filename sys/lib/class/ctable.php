<?php
    function createTable($tbl, $emptyTable=0)
    {
        global $msgNoRec;

        $cols = count($tbl['header']);
        $rows = count($tbl['row']);

        if (!$emptyTable && $rows == 0)
            return "<div style='text-align:center; margin: 10px 0'>$msgNoRec</div>";

        $h = "
			<div class=tableBox>
				<table class=table>
					<tr class=header>";

        for ($i=0; $i<$cols; $i++)
        {
            $width[$i]    = ($tbl['width'][$i] == "") ? "" : "width={$tbl['width'][$i]}";
            $headerAlign[$i]    = ($tbl['headerAlign'][$i] == "") ? "align=center" : "align={$tbl['headerAlign'][$i]}";
			$rowAlign[$i]    = ($tbl['rowAlign'][$i] == "") ? "align=center" : "align={$tbl['rowAlign'][$i]}";
            $fontSize[$i] = ($tbl['fontSize'][$i] == "") ? "" : "style='font-size:{$tbl['fontSize'][$i]}'";
            $h .= "<td class=td {$width[$i]} {$headerAlign[$i]}>{$tbl['header'][$i]}</td>";
        }
        $h .= "</tr>";

        if ($rows == 0)
            return "$h <tr class=row2><td colspan=$cols align=center>$msgNoRec</td></tr></table></div>";

        $span = 0;
        for ($r=0; $r<$rows; $r++)
        {
            if (count($tbl['row'][$r]) == 1)
            {
                /* special for block:none */
                $h .= "<tr><td valign=top colspan=$cols>{$tbl['row'][$r][0]}</td></tr>";
                $span ++;
                continue;
            }

            $cls = (($r-$span) % 2) ? "row" : "row2";
            $h .= "<tr class=$cls onmouseover='this.className=\"rowOver\"' onmouseout='this.className=\"$cls\"'>";
            for ($i=0; $i<$cols; $i++)
            {
                $h .= "<td class=td valign=top {$width[$i]} {$rowAlign[$i]} {$fontSize[$i]}>{$tbl['row'][$r][$i]}</td>";
            }
            $h .= "</tr>";
        }
        $h .= "
					</table>
				</div>";
		return $h;
    }
    
    function createTable2($tbl)
    {
        $cols = count($tbl[header]);
        $h = "<div class=tableBox>
				<div>
					<table class=table>
						<tr class=header>";
        
        for ($i=0; $i<$cols; $i++)
        {
            $width[$i]    = ($tbl[width][$i] == "") ? "" : "width={$tbl[width][$i]}";
			$headerAlign[$i] = ($tbl[headerAlign][$i] == "") ? "align=center" : "align={$tbl[headerAlign][$i]}";
            $rowAlign[$i] = ($tbl[rowAlign][$i] == "") ? "align=center" : "align={$tbl[rowAlign][$i]}";
            $fontSize[$i] = ($tbl[fontSize][$i] == "") ? "" : "style='font-size:{$tbl[fontSize][$i]}'";
            $h .= "<td class=td {$width[$i]} {$headerAlign[$i]}>{$tbl[header][$i]}</td>";
        }
        $h .= "  		</tr>
					</table>
				</div>";
        
        
        $rows = count($tbl[row]); $span=0;
        for ($r=0; $r<$rows; $r++)
        {
            if (count($tbl[row][$r]) == 1)
            {
                /* special for block:none */
				$h .= $tbl[row][$r][0];
                $span ++;
                continue;
            }
            
            $cls = (($r-$span) % 2) ? "row" : "row2";
            $h .= "<div><table class=table><tr class=$cls onmouseover='this.className=\"rowOver\"' onmouseout='this.className=\"$cls\"'>";
            for ($i=0; $i<$cols; $i++)
            {
                $h .= "<td class=td valign=top {$width[$i]} {$rowAlign[$i]} {$fontSize[$i]}>{$tbl[row][$r][$i]}</td>";
            }
            $h .= "</tr></table></div>";
        }
        $h .= "</div>";
		return $h;
    }
?>