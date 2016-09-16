<?php

/**
 * 
 * @param object $item will exist
 * @return string
 */
function equipment_management_show_id_shortcode( $item ) {
    
    // Doing setup work
    
    // The html for the current and past use tables
    $use_html = ""; 
    // We are doing this first, so that we already have a value for how many
    // items are in use and don't need to loop twice later
    $amount_in_use = 0;
    
    $history = $item->attrs["use"]->history; // Just so we don't need to query $item every time
    
    $use_html.="<h2>Einsätze</h2>";
    if(empty($history)) { //aka if this item is not, and never was, used
        $use_html.="Dieses Item wurde noch nie eingesetzt";
    } else {
        $current_use = array();
    
        $use_histroy = array();

        foreach ($history as $use) {
            if($use['date_back'] == "1000-01-01 00:00:00") {
                array_push($current_use, $use);
            } else {
                array_push($use_histroy, $use);
            }
        }
        
        if(!empty($current_use)) {
            $use_html.="<h3>Momentaner Einsatz</h3>";
            $use_html.="<table>";
                $use_html.="<tr>";
                    $use_html.="<th>Benutzer</th>";
                    $use_html.="<th>Menge</th>";
                    $use_html.="<th>Seit</th>";
                $use_html.="</tr>";
                foreach ($current_use as $use) {
                    $use_html.="<tr>";
                        $use_html.="<td>" . $use['used_by'] . "</td>";
                        $use_html.="<td>" . $use['amount_used'] . "</td>";
                        $use_html.="<td>" . $use['date_used'] . "</td>";
                    $use_html.="</tr>";
                    $amount_in_use += $use['amount_used'];
                }
                
            $use_html.="</table>";
        }
        
        if(!empty($use_histroy)) {
            $use_html.="<h3>Vergangene Einsätze</h3>";
            $use_html.="<table>";
                $use_html.="<tr>";
                    $use_html.="<th>Benutzer</th>";
                    $use_html.="<th>Menge</th>";
                    $use_html.="<th>Start</th>";
                    $use_html.="<th>Ende</th>";
                $use_html.="</tr>";
                foreach ($use_histroy as $use) {
                    $use_html.="<tr>";
                        $use_html.="<td>" . $use['used_by'] . "</td>";
                        $use_html.="<td>" . $use['amount_used'] . "</td>";
                        $use_html.="<td>" . $use['date_used'] . "</td>";
                        $use_html.="<td>" . $use['date_back'] . "</td>";
                    $use_html.="</tr>";
                }
                
            $use_html.="</table>";
        }
        
    }
    
    // Finished setup work
    
    // The HTML returned
    $html = "";
    
    // Formatted as HTML
    $html.= "<div>";
    
        $html.="<h1>" . $item->attrs['name'] . "</h1>"; //Item Name as heading
    
        $html.="<table>"; //Table for more specific item attributes
            $html.="<tr>";
                $html.="<td>ID</td>";
                $html.="<td>" . $item->attrs['id'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Kategorie</td>";
                $html.="<td>" . $item->attrs['category'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Spezifikation</td>";
                $html.="<td>" . $item->attrs['specification'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Kategoriespezifische Angabe</td>";
                $html.="<td>" . $item->attrs['category_tags'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Einsatz/Verbindung</td>";
                $html.="<td>" . $item->attrs['application'] . " </td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Notiz</td>";
                $html.="<td>" . $item->attrs['notes'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Kaufdatum</td>";
                $html.="<td>" . $item->attrs['date_bought'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Kaufnotiz</td>";
                $html.="<td>" . $item->attrs['bought_note'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Neupreis</td>";
                $html.="<td>" . $item->attrs['price'] . "€</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Händler</td>";
                $html.="<td>" . $item->attrs['vendor'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Artikelnummer beim Händler</td>";
                $html.="<td>" . $item->attrs['vendor_item_id'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Insgesammt vorhandene Anzahl</td>";
                $html.="<td>" . $item->attrs['amount'] . "</td>";
            $html.="</tr>";
            $html.="<tr>";
                $html.="<td>Verfügbare Anzahl</td>";
                $unused_amount = $item->attrs['amount'] - $amount_in_use;
                $html.="<td>" . $unused_amount . "</td>";
            $html.="</tr>";
        $html.="</table>";
        
        $html.=$use_html;
        
    $html.= "</div>";
    return $html;
}