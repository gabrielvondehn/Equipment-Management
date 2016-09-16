var number_of_new_items = 0;

jQuery(document).ready(function( $ ) {
    $('#eq_use_add_new').click(function( event ) {
        var table = $('#eq_use_table');
        number_of_new_items++;
        var new_rows = '<tr id="eq_use_row_new_' + number_of_new_items + '"><td><input type="text" id="eq_use_used_by_new_' + number_of_new_items + '" name="eq_use_used_by_new_' + number_of_new_items + '"></td><td><input type="number" step="1" id="eq_use_amount_used_new_' + number_of_new_items + '" name="eq_use_amount_used_new_' + number_of_new_items + '"></td><td><input type="date" id="eq_use_date_used_new_' + number_of_new_items + '" name="eq_use_date_used_new_' + number_of_new_items + '"></td><td><input type="date" id="eq_use_date_back_new_' + number_of_new_items + '" name="eq_use_date_back_new_' + number_of_new_items + '"></td></tr>';
        table.append(new_rows); 
    });
});

/*
    <tr id="eq_use_row_new_1">
        <td><input type="text" id="eq_use_used_by_new_1" name="eq_use_used_by_new_1"></td>
        <td><input type="number" step="1" id="eq_use_amount_used_new_1" name="eq_use_amount_used_new_1"></td>
        <td><input type="date" id="eq_use_date_used_new_1" name="eq_use_date_used_new_1"></td>
        <td><input type="date" id="eq_use_date_back_new_1" name="eq_use_date_back_new_1"></td>
    </tr>*/