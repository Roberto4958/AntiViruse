$(document).ready(function(){
    // DOM ready
 $("#AddOrStore").change(function() {
      SelectedChanged();
    });
    $('#virus-title').hide();
    
});


function SelectedChanged(){
    if('store' == $('#AddOrStore').find(":selected").val()){
        $('#virus-title').show();
    }
    else $('#virus-title').hide();
}






