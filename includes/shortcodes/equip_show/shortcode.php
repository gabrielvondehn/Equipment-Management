<?php

/**
 * 
 * @param object $item will exist
 * @return string
 */
function equipment_management_show_id_shortcode( $item ) {
    
    global $equipment_management;
    $security = $equipment_management->security;
    
    if(!$security->current_user_can("view_id")) {
        return "Sorry, you don't have permissions to view this page.";
    }
    
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
            if($use['date_back'] == "1000-01-01") {
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
    
    // Construct array containing all attributes & html; non-attributes are prefixed with a "_"
    $htmlarr = array();
    
    $htmlarr['_div_start'] = "<div>";
    $htmlarr['equip_name'] = "<h1>" . $item->attrs['name'] . "</h1>"; //Item Name as heading
    $htmlarr['_table_start'] = "<table>"; //Table for more specific item attributes
    $htmlarr['id'] ="<tr><td>ID</td><td>" . $item->attrs['id'] . "</td></tr>";
    $htmlarr['equip_category'] = "<tr><td>Kategorie</td><td>" . $item->attrs['category'] . "</td></tr>";
    $htmlarr['equip_specitication'] = "<tr><td>Spezifikation</td><td>" . $item->attrs['specification'] . "</td></tr>";
    $htmlarr['equip_category_tags'] = "<tr><td>Kategoriespezifische Angabe</td><td>" . $item->attrs['category_tags'] . "</td></tr>";
    $htmlarr['equip_application'] = "<tr><td>Einsatz/Verbindung</td><td>" . $item->attrs['application'] . " </td></tr>";
    $htmlarr['equip_notes'] = "<tr><td>Notiz</td><td>" . $item->attrs['notes'] . "</td></tr>";
    $htmlarr['date_bought'] = "<tr><td>Kaufdatum</td><td>" . $item->attrs['date_bought'] . "</td></tr>";
    $htmlarr['bought_note'] = "<tr><td>Kaufnotiz</td><td>" . $item->attrs['bought_note'] . "</td></tr>";
    $htmlarr['equip_price'] = "<tr><td>Neupreis</td><td>" . $item->attrs['price'] . "€</td></tr>";
    $htmlarr['equip_vendor'] = "<tr><td>Händler</td><td>" . $item->attrs['vendor'] . "</td></tr>";
    $htmlarr['vendor_item_id'] = "<tr><td>Artikelnummer beim Händler</td><td>" . $item->attrs['vendor_item_id'] . "</td></tr>";
    $htmlarr['equip_amount'] = "<tr><td>Insgesammt vorhandene Anzahl</td><td>" . $item->attrs['amount'] . "</td></tr>";
    $unused_amount = $item->attrs['amount'] - $amount_in_use;
    $htmlarr['equip_in_use'] = "<tr><td>Verfügbare Anzahl</td><td>" . $unused_amount . "</td></tr>";
    $htmlarr['_table_end'] = "</table>";
    $htmlarr['equip_history'] = $use_html; 
    $htmlarr['_div_end'] = "</div>";
    
    
    // The final HTML returned
    $html = "";
    
    // Add html from the array only if user has permission
    foreach($htmlarr as $attr => $val) {
        if(substr($attr, 0, 1) == "_") { // A enries starting with _ are necessitated by HTML and don't have permissions
            $html.=$val;
        } else {
            if($security->current_user_can("view_".$attr)) {
                $html.=$val;
            }
        }
    }
    return $html;
}