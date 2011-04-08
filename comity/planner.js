
function planner_add_date(){
    var day = document.getElementById('date');
    var from = document.getElementById('from_time');
    var to = document.getElementById('to_time');
    var list = document.getElementById('list');

    if(day.value == ''){
        //Do nothing
    }
    else{
        var text = day.value + ' ' + from.value + ' - ' + to.value;
        var value = day.value + '@' + from.value + '@' + to.value;

        var option = new Option(text,value);

        try{
            list.add(option, null);    //Gecko
        }
        catch(e){
            list.add(option);          //IE
        }
    }
}

function planner_remove_date(){
    var list = document.getElementById('list');

    for(var i=0; i<list.length; i++){
        if(list.options[i].selected){
            list.remove(i);
        }
    }
}

function planner_submit(){
    var name = document.getElementById('name');
    var nameerror = document.getElementById('nameerror');

    //Check name
    if(name.value == ''){
        nameerror.innerHTML = '*Vous devez fournir un nom';
        return false;
    }
    else{
        nameerror.innerHTML = '';
    }

    //Check dates
    var list = document.getElementById('list');
    var listerror = document.getElementById('listerror');

    if(list.length == 0){
        listerror.innerHTML = '*Vous devez fournir au moins une date';
        return false;
    }
    else{
        listerror.innerHTML = '';
    }

    //Validation complete

    //Check all dates in list
    for(var i = 0; i<list.length; i++){
        list.options[i].selected = true;
    }

    document.newplanner.submit();
}