$(document).ready ( function () {
   
    $("header span").find(".active").removeClass("active");
    
    //changer le contenu des parametres
    $("#sous-menu-setting li").click( function() {
       var id = $(this).attr('class');

       $(".section").removeClass("put");
       $("form").hide();
       $('#section_'+id).addClass("put");
       //exception pour data
       if (id ==="data") {
           $("#form_import").show();
           $("#form_export").show();
       }
    });
    
    //changer contenu des statistiques
    $("#sous-menu-stat li").click( function() {
       var id = $(this).attr('class');
       var swimmer = $('.search_swimmer').val();
       var meeting = $('.search_competition').val();
       var race = $('.search_race').val();
       var season = $('#param-season li.selected').attr('id');
       
       $('#type-stat').val(id);
       
       switch (id) {
            case 'repartition':
                createRepartition(swimmer,meeting,race,season);
                break;
            case 'performance':
                createPerformance(swimmer,meeting,race,season);
                break;
            case 'planning':
                createPlanification(swimmer,race,season);
                break;
       }

       $(".section").removeClass("put");
       $('#section_'+id).addClass("put");
    });
    $('#button-valid-stat').click( function() {
       var id = $('#type-stat').val();
       var swimmer = $('.search_swimmer').val();
       var meeting = $('.search_competition').val();
       var race = $('.search_race').val();
       var season = $('#param-season li.selected').attr('id');
       
        switch (id) {
            case 'repartition':
                createRepartition(swimmer,meeting,race,season);
                break;
            case 'performance':
                createPerformance(swimmer,meeting,race,season);
                break;
            case 'planning':
                createPlanification(swimmer,race,season);
                break;
        }
    });
    $('#param-season li').click( function() {
       var id = $('#type-stat').val();
       var swimmer = $('.search_swimmer').val();
       var meeting = $('.search_competition').val();
       var race = $('.search_race').val();
       var season = $(this).attr('id');
       
        switch (id) {
            case 'repartition':
                createRepartition(swimmer,meeting,race,season);
                break;
            case 'performance':
                createPerformance(swimmer,meeting,race,season);
                break;
            case 'planning':
                createPlanification(swimmer,race,season);
                break;
        }
    });
    
    //pour choix season
    $('#param-season li').click( function () {
        $("#param-season").find(".selected").removeClass("selected");
        $(this).addClass('selected'); 
    });
    
    //annulation
    $(".form_cancel").click( function() {
       var id = $(this).parents("form").children("input[type='hidden']").val();

       $('#section_'+id).addClass("put");
       $(this).parents("form").hide();
    });
    
    //bouton retour
    $(".section button").click( function() {
        var id = $(this).attr('name');
        
        $(this).parent().removeClass("put");
        $("#form_"+id).show();
    });
    
    //changer onglet actif
    $(".sous-menu li").click( function() {
        var id = $(this).attr('id');
        
        $(".sous-menu li").removeClass("active");
        $(this).addClass("active");
    }); 
    
    //pour import/export au passage souris
    $("#data_import").mousemove( function() {
       
        $("#import button").css( {
            'background-color': '#D4DADC',
            'color': '#212121'
        });
    });
    $("#data_import").mouseout( function() {
       
        $("#import button").css( {
            'background-color': '#046675',
            'color': '#FFF'
        });
    });
    
    //pour changement dans recherche
    $('.search_swimmer').change( function() {
       var id = $('#type-stat').val();
       if ($(this).val() !== "" && id !== "planning")
           $('.search_race option:eq(0)').prop('selected', true);
    });
    //pour changement dans recherche
    $('.search_race').change( function() {
       var id = $('#type-stat').val();
       if ($(this).val() !== "" && id !== "planning")
           $('.search_swimmer option:eq(0)').prop('selected', true);
    });
});

function createRepartition(nageur,compet,course,saison) {
    //vider le graph
    $('#graph-repartition').html('');
    
    $.ajax({  
        //On utilise de l'ajax
        type: "POST",  //En post (envoi de données)
        url: '../model/fonctions_request_stat.php', //On va chercher le fichier php
        data: "style=repartition&nageur="+nageur+"&compet="+compet+"&course="+course+"&saison="+saison, //On transmet les deux données pour l'exécution de la requête
        success: function(data) { 

            //Si le php renvoie quelque chose
            var tab=$.parseJSON(data);

            //verification contenu
            if (!tab)
                $('#pb-repartition').text("La recherche n'a pas abouti");
            else {
                $('#pb-repartition').text("");
                
                //remplissage des titres
                var swimmer = $('.search_swimmer option:selected').text();
                var meeting = $('.search_competition option:selected').text();
                var race = $('.search_race option:selected').text();

                $('#repartition-title').text(meeting);
                if (course === "")
                    $('#repartition-swim-race').html('<span>'+swimmer+'</span>');
                else if (nageur === "")
                    $('#repartition-swim-race').html('<span>'+race+'</span>');
                
                //initialisation de toutes les variables necessaires
                var i=0;
                var div; var divTitle; var divContent;
                var swim; var roun; var rac; 
                var chaine_data = new Object(); var total = new Array();

                while (i < tab.length) {
                    /*SI RECHERCHE PAR RACE*/
                    if (nageur === "" && course !== "") {
                        /*INITIALISATION*/
                        if (i===0) {
                            //variable nageur et round
                            swim = tab[i].swimmer;
                            roun = tab[i].round;

                            //div necessaire
                            div = $('<div class="graph-div"/>');
                            divTitle = $('<div class="graph-title"/>');
                            divContent = $('<div class="graph-content"/>');
                            
                            //remplissage de la vue
                            divTitle.text(swim).appendTo(div);
                            divContent.appendTo(div);
                            div.appendTo('#graph-repartition');

                            //remplissage objet et tableau objet
                            chaine_data = {label:roun,value:tab[i].percent};
                            total.push(chaine_data);
                        } else {
                            //si toujours meme nageur
                            if (swim === tab[i].swimmer) {
                                    //si meme round
                                    if (roun === tab[i].round) {
                                        //remplissage objet et tableau objet
                                        chaine_data = {label:roun,value:tab[i].percent};
                                        total.push(chaine_data);
                                    //si round different
                                    } else {
                                        //creation donut
                                        var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                        divDonut.appendTo(divContent);                              

                                        Morris.Donut({
                                          element: 'donut-'+i,
                                          data: total,
                                          colors: ['#FFF'],
                                          formatter: function (y) {  return y + '%' }
                                        });

                                        //changement de round et vidage tableau
                                        roun = tab[i].round;
                                        total = [];
                                        //remplissage objet et tableau objet
                                        chaine_data = {label:roun,value:tab[i].percent};
                                        total.push(chaine_data);
                                    }

                                    //si dernier tour
                                    if (i === (tab.length-1)) {
                                        //creation donut
                                        var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                        divDonut.appendTo(divContent);  

                                        Morris.Donut({
                                          element: 'donut-'+i,
                                          data: total,
                                          colors: ['#FFF'],
                                          formatter: function (y) {  return y + '%' }
                                        });

                                    }
                            //si nageur different
                            } else {
                                //creation donut
                                var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                divDonut.appendTo(divContent);

                                Morris.Donut({
                                  element: 'donut-'+i,
                                  data: total,
                                  colors: ['#FFF'],
                                  formatter: function (y) {  return y + '%' }
                                });

                                //variable nageur et round
                                swim = tab[i].swimmer;
                                roun = tab[i].round;

                                //div necessaire
                                div = $('<div class="graph-div"/>');
                                divTitle = $('<div class="graph-title"/>');
                                divContent = $('<div class="graph-content"/>');
                                
                                //remplissage de la vue
                                divTitle.text(swim).appendTo(div);
                                divContent.appendTo(div);
                                div.appendTo('#graph-repartition');

                                //vidage tableau/remplissage objet et tableau objet
                                total =[];
                                chaine_data = {label:roun,value:tab[i].percent};
                                total.push(chaine_data);

                                //si dernier tour
                                if (i === (tab.length-1)) {

                                    //creation donut
                                    var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                    divDonut.appendTo(divContent); 

                                    Morris.Donut({
                                      element: 'donut-'+i,
                                      data: total,
                                      colors: ['#FFF'],
                                      formatter: function (y) {  return y + '%' }
                                    });

                                }
                            }

                        }
                    /*SI RECHERCHE PAR NAGEUR OU PREMIER AFFICHAGE*/
                    } else if (course === "" || (course === "" && nageur === "")) {
                        /*INITIALISATION*/
                        if (i===0) {
                            //variable nageur et round
                            rac = tab[i].race;
                            roun = tab[i].round;

                            //div necessaire
                            div = $('<div class="graph-div"/>');
                            divTitle = $('<div class="graph-title"/>');
                            divContent = $('<div class="graph-content"/>');
                            
                            //remplissage de la vue
                            divTitle.text(rac).appendTo(div);
                            divContent.appendTo(div);
                            div.appendTo('#graph-repartition');

                            //remplissage objet et tableau objet
                            chaine_data = {label:roun,value:tab[i].percent};
                            total.push(chaine_data);
                        } else {
                            //si toujours meme nageur
                            if (rac === tab[i].race) {
                                    //si meme round
                                    if (roun === tab[i].round) {
                                        //remplissage objet et tableau objet
                                        chaine_data = {label:roun,value:tab[i].percent};
                                        total.push(chaine_data);
                                    //si round different
                                    } else {
                                        //creation donut
                                        var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                        divDonut.appendTo(divContent);                              

                                        Morris.Donut({
                                          element: 'donut-'+i,
                                          data: total,
                                          colors: ['#FFF'],
                                          formatter: function (y) {  return y + '%' }
                                        });

                                        //changement de round et vidage tableau
                                        roun = tab[i].round;
                                        total = [];
                                        //remplissage objet et tableau objet
                                        chaine_data = {label:roun,value:tab[i].percent};
                                        total.push(chaine_data);
                                    }

                                    //si dernier tour
                                    if (i === (tab.length-1)) {
                                        //creation donut
                                        var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                        divDonut.appendTo(divContent);  

                                        Morris.Donut({
                                          element: 'donut-'+i,
                                          data: total,
                                          colors: ['#FFF'],
                                          formatter: function (y) {  return y + '%' }
                                        });

                                    }
                            //si nageur different
                            } else {
                                //creation donut
                                var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                divDonut.appendTo(divContent);

                                Morris.Donut({
                                  element: 'donut-'+i,
                                  data: total,
                                  colors: ['#FFF'],
                                  formatter: function (y) {  return y + '%' }
                                });

                                //variable nageur et round
                                rac = tab[i].race;
                                roun = tab[i].round;

                                //div necessaire
                                div = $('<div class="graph-div"/>');
                                divTitle = $('<div class="graph-title"/>');
                                divContent = $('<div class="graph-content"/>');
                                
                                //remplissage de la vue
                                divTitle.text(rac).appendTo(div);
                                divContent.appendTo(div);
                                div.appendTo('#graph-repartition');

                                //vidage tableau/remplissage objet et tableau objet
                                total =[];
                                chaine_data = {label:roun,value:tab[i].percent};
                                total.push(chaine_data);

                                //si dernier tour
                                if (i === (tab.length-1)) {

                                    //creation donut
                                    var divDonut= $('<div id="donut-'+i+'" class="donuts"/>');
                                    divDonut.appendTo(divContent); 

                                    Morris.Donut({
                                      element: 'donut-'+i,
                                      data: total,
                                      colors: ['#FFF'],
                                      formatter: function (y) {  return y + '%' }
                                    });

                                }
                            }

                        }
                    }
                    i++;	
                }
            }                       
        }
    });
}
function createPerformance(nageur,compet,course,saison) {
    //vider le graph
    $('#graph-performance').html('');
    
    $.ajax({  
        //On utilise de l'ajax
        type: "POST",  //En post (envoi de données)
        url: '../model/fonctions_request_stat.php', //On va chercher le fichier php
        data: "style=performance&nageur="+nageur+"&compet="+compet+"&course="+course+"&saison="+saison, //On transmet les deux données pour l'exécution de la requête
        success: function(data) { 
            //Si le php renvoie quelque chose
            var tab=$.parseJSON(data);
            
            //verification contenu
            if (!tab)
                $('#pb-performance').text("La recherche n'a pas abouti");
            else {
                $('#pb-performance').text("");
                
                //remplissage des titres
                var swimmer = $('.search_swimmer option:selected').text();
                var meeting = $('.search_competition option:selected').text();
                var race = $('.search_race option:selected').text();

                $('#performance-title').text(meeting);
                if (course === "")
                    $('#performance-swim-race').html('<span>'+swimmer+'</span>');
                else if (nageur === "")
                    $('#performance-swim-race').html('<span>'+race+'</span>');

                //initialisation de toutes les variables necessaires
                var i=0;
                var div; var divTitle; var divContent;
                var swim; var roun; var rac; 
                var chaine_data; var total = new Array();

                while (i < tab.length) {
                    /*SI RECHERCHE PAR RACE*/
                    if (nageur === "" && course !== "") {
                        /*INITIALISATION*/
                        if (i===0) {
                            //variable nageur et round
                            swim = tab[i].swimmer;
                            roun = tab[i].round;

                            //div necessaire
                            div = $('<div class="graph-div"/>');
                            divTitle = $('<div class="graph-title"/>');
                            divContent = $('<div class="graph-content"/>');

                            //remplissage de la vue
                            divTitle.text(swim).appendTo(div);
                            divContent.appendTo(div);
                            div.appendTo('#graph-performance');

                            //remplissage nbr pourcent
                            chaine_data = tab[i].percent+' %';
                        } else {
                            //si toujours meme nageur
                            if (swim === tab[i].swimmer) {
                                    /*A CHAQUE TOUR CHANGEMENT DE ROUND*/
                                    //creation pourcentage
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);                              

                                    var divText= $('<div class="graph-text"/>');
                                    var divSousText= $('<p/>');
                                    divText.text(chaine_data).appendTo(divPourc);
                                    divSousText.text(roun).appendTo(divPourc);
                                    divPourc.appendTo(divContent);
                                    
                                    //changement de round
                                    roun = tab[i].round;
                                    //remplissage nbr pourcent
                                    chaine_data = tab[i].percent+' %';

                                    //si dernier tour
                                    if (i === (tab.length-1)) {
                                        //creation pourcentage
                                        var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                        divPourc.appendTo(divContent);                              

                                        var divText= $('<div class="graph-text"/>');
                                        var divSousText= $('<p/>');
                                        divText.text(chaine_data).appendTo(divPourc);
                                        divSousText.text(roun).appendTo(divPourc);
                                        divPourc.appendTo(divContent);

                                    }
                            //si nageur different
                            } else {
                                //creation pourcentage
                                var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                divPourc.appendTo(divContent);                              

                                var divText= $('<div class="graph-text"/>');
                                var divSousText= $('<p/>');
                                divText.text(chaine_data).appendTo(divPourc);
                                divSousText.text(roun).appendTo(divPourc);
                                divPourc.appendTo(divContent);

                                //variable nageur et round
                                swim = tab[i].swimmer;
                                roun = tab[i].round;

                                //div necessaire
                                div = $('<div class="graph-div"/>');
                                divTitle = $('<div class="graph-title"/>');
                                divContent = $('<div class="graph-content"/>');

                                //remplissage de la vue
                                divTitle.text(swim).appendTo(div);
                                divContent.appendTo(div);
                                div.appendTo('#graph-performance');

                                //remplissage nbr pourcent
                                chaine_data = tab[i].percent+' %';

                                //si dernier tour
                                if (i === (tab.length-1)) {

                                    //creation pourcentage
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);                              

                                    var divText= $('<div class="graph-text"/>');
                                    var divSousText= $('<p/>');
                                    divText.text(chaine_data).appendTo(divPourc);
                                    divSousText.text(roun).appendTo(divPourc);
                                    divPourc.appendTo(divContent);

                                }
                            }
                        }
                    /*SI RECHERCHE PAR NAGEUR*/    
                    } else if (course === "" && nageur !== "") {
                        /*INITIALISATION*/
                        if (i===0) {
                            //variable race et round
                            rac = tab[i].race;
                            roun = tab[i].round;

                            //div necessaire
                            div = $('<div class="graph-div"/>');
                            divTitle = $('<div class="graph-title"/>');
                            divContent = $('<div class="graph-content"/>');

                            //remplissage de la vue
                            divTitle.text(rac).appendTo(div);
                            divContent.appendTo(div);
                            div.appendTo('#graph-performance');

                            //remplissage nbr pourcent
                            chaine_data = tab[i].percent+' %';
                        } else {
                            //si toujours meme race
                            if (rac === tab[i].race) {
                                /*A CHAQUE TOUR CHANGEMENT DE ROUND*/
                                //creation pourcentage
                                var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                divPourc.appendTo(divContent);                              

                                var divText= $('<div class="graph-text"/>');
                                var divSousText= $('<p/>');
                                divText.text(chaine_data).appendTo(divPourc);
                                divSousText.text(roun).appendTo(divPourc);
                                divPourc.appendTo(divContent);

                                //changement de round
                                roun = tab[i].round;
                                //remplissage nbr pourcent
                                chaine_data = tab[i].percent+' %';

                                //si dernier tour
                                if (i === (tab.length-1)) {
                                    //creation pourcentage
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);                              

                                    var divText= $('<div class="graph-text"/>');
                                    var divSousText= $('<p/>');
                                    divText.text(chaine_data).appendTo(divPourc);
                                    divSousText.text(roun).appendTo(divPourc);
                                    divPourc.appendTo(divContent);

                                }
                            //si race different
                            } else {
                                //creation pourcentage
                                var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                divPourc.appendTo(divContent);                              

                                var divText= $('<div class="graph-text"/>');
                                var divSousText= $('<p/>');
                                divText.text(chaine_data).appendTo(divPourc);
                                divSousText.text(roun).appendTo(divPourc);
                                divPourc.appendTo(divContent);

                                //variable race et round
                                rac = tab[i].race;
                                roun = tab[i].round;

                                //div necessaire
                                div = $('<div class="graph-div"/>');
                                divTitle = $('<div class="graph-title"/>');
                                divContent = $('<div class="graph-content"/>');

                                //remplissage de la vue
                                divTitle.text(rac).appendTo(div);
                                divContent.appendTo(div);
                                div.appendTo('#graph-performance');

                                //remplissage nbr pourcent
                                chaine_data = tab[i].percent+' %';

                                //si dernier tour
                                if (i === (tab.length-1)) {
                                    //creation pourcentage
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);                              

                                    var divText= $('<div class="graph-text"/>');
                                    var divSousText= $('<p/>');
                                    divText.text(chaine_data).appendTo(divPourc);
                                    divSousText.text(roun).appendTo(divPourc);
                                    divPourc.appendTo(divContent);

                                }
                            }
                        }
                    /*SI PREMIER AFFICHAGE*/    
                    } else if (course === "" && nageur === "") {
                        /*INITIALISATION*/
                        if (i===0) {
                            //variable race et round
                            rac = tab[i].race;
                            swim = tab[i].swimmer;
                            roun = tab[i].round;

                            //div necessaire
                            div = $('<div class="graph-div"/>');
                            divTitle = $('<div class="graph-title"/>');
                            divContent = $('<div class="graph-content"/>');

                            //remplissage de la vue
                            divTitle.text(rac).appendTo(div);
                            divContent.appendTo(div);
                            div.appendTo('#graph-performance');

                            //remplissage nbr pourcent                            
                            chaine_data = {round:roun,percent:tab[i].percent};
                            total.push(chaine_data);
                        } else {
                            //si toujours meme race
                            if (rac === tab[i].race) {
                                if (swim === tab[i].swimmer) { 
                                    /*A CHAQUE TOUR CHANGEMENT DE ROUND*/
                                    //changement de round
                                    roun = tab[i].round;
                                    
                                    //Remplir le tableau
                                    chaine_data = {round:roun,percent:tab[i].percent};
                                    total.push(chaine_data);
                                    
                                    //si dernier tour
                                    if (i === (tab.length-1)) {
                                        //creation pourcentage
                                        var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                        divPourc.appendTo(divContent);    
                                        var divSousTitle = $('<div/>');
                                        divSousTitle.text(swim).appendTo(divPourc);
                                        var divTable= $('<table/>');
                                        var th = $('<thead><th>Round</th><th>Perf</th></thead>');
                                        th.appendTo(divTable);
                                        var body = $('<tbody/>');

                                        //calcul de la moyenne
                                        var temp=0;
                                        for (var j=0; j <total.length;j++) {
                                           temp = temp + parseInt(total[j].percent);

                                           var tr = $('<tr/>');                                       
                                           var td1 = $('<td/>');
                                           td1.text(total[j].round).appendTo(tr);
                                           var td2 = $('<td/>');
                                           td2.text(total[j].percent+' %').appendTo(tr);
                                           tr.appendTo(body);                                      
                                        }
                                        body.appendTo(divTable);

                                        var moy = Math.round(temp/total.length*100)/100;
                                        var divText= $('<div class="graph-text-general"/>');
                                        divText.text(moy+' %').appendTo(divPourc);
                                        divTable.appendTo(divPourc);
                                        divPourc.appendTo(divContent);

                                    }
                                } else {                                   
                                    //creation pourcentage 
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);             
                                    var divSousTitle = $('<div/>');
                                    divSousTitle.text(swim).appendTo(divPourc);
                                    var divTable= $('<table/>');
                                    var th = $('<thead><th>Round</th><th>Perf</th></thead>');
                                    th.appendTo(divTable);
                                    var body = $('<tbody/>');
                                    
                                    //calcul de la moyenne
                                    var temp=0;
                                    for (var j=0; j <total.length;j++) {
                                       temp = temp + parseInt(total[j].percent);
                                       
                                       var tr = $('<tr/>');                                       
                                       var td1 = $('<td/>');
                                       td1.text(total[j].round).appendTo(tr);
                                       var td2 = $('<td/>');
                                       td2.text(total[j].percent+' %').appendTo(tr);
                                       tr.appendTo(body);                                      
                                    }
                                    body.appendTo(divTable);
                                    
                                    var moy = Math.round(temp/total.length*100)/100;
                                    var divText= $('<div class="graph-text-general"/>');
                                    divText.text(moy+' %').appendTo(divPourc);
                                    divTable.appendTo(divPourc);
                                    divPourc.appendTo(divContent);

                                    //changement de round
                                    swim = tab[i].swimmer;
                                    roun = tab[i].round;
                                    //remplissage nbr pourcent
                                    total=[];
                                    chaine_data = {round:roun,percent:tab[i].percent};
                                    total.push(chaine_data);

                                    //si dernier tour
                                    if (i === (tab.length-1)) {
                                        //creation pourcentage
                                        var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                        divPourc.appendTo(divContent);    
                                        var divSousTitle = $('<div/>');
                                        divSousTitle.text(swim).appendTo(divPourc);
                                        var divTable= $('<table/>');
                                        var th = $('<thead><th>Round</th><th>Perf</th></thead>');
                                        th.appendTo(divTable);
                                        var body = $('<tbody/>');

                                        //calcul de la moyenne
                                        var temp=0;
                                        for (var j=0; j <total.length;j++) {
                                           temp = temp + parseInt(total[j].percent);

                                           var tr = $('<tr/>');                                       
                                           var td1 = $('<td/>');
                                           td1.text(total[j].round).appendTo(tr);
                                           var td2 = $('<td/>');
                                           td2.text(total[j].percent+' %').appendTo(tr);
                                           tr.appendTo(body);                                      
                                        }
                                        body.appendTo(divTable);

                                        var moy = Math.round(temp/total.length*100)/100;;
                                        var divText= $('<div class="graph-text-general"/>');
                                        divText.text(moy+' %').appendTo(divPourc);
                                        divTable.appendTo(divPourc);
                                        divPourc.appendTo(divContent);

                                    }
                                }
                            //si race different
                            } else {
                                //creation pourcentage
                                var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                divPourc.appendTo(divContent);       
                                var divSousTitle = $('<div/>');
                                divSousTitle.text(swim).appendTo(divPourc);
                                var divTable= $('<table/>');
                                var th = $('<thead><th>Round</th><th>Perf</th></thead>');
                                th.appendTo(divTable);
                                var body = $('<tbody/>');

                                //calcul de la moyenne
                                var temp=0;  
                                for (var j=0; j <total.length;j++) {
                                   temp = temp + parseInt(total[j].percent);

                                   var tr = $('<tr/>');                                       
                                   var td1 = $('<td/>');
                                   td1.text(total[j].round).appendTo(tr);
                                   var td2 = $('<td/>');
                                   td2.text(total[j].percent+' %').appendTo(tr);
                                   tr.appendTo(body);                                      
                                }
                                body.appendTo(divTable);

                                var moy = Math.round(temp/total.length*100)/100;
                                var divText= $('<div class="graph-text-general"/>');
                                divText.text(moy+' %').appendTo(divPourc);
                                divTable.appendTo(divPourc);
                                divPourc.appendTo(divContent);
                                    
                                //variable race et round
                                rac = tab[i].race;                              
                                swim = tab[i].swimmer;
                                roun = tab[i].round;

                                //div necessaire
                                div = $('<div class="graph-div"/>');
                                divTitle = $('<div class="graph-title"/>');
                                divContent = $('<div class="graph-content"/>');

                                //remplissage de la vue
                                divTitle.text(rac).appendTo(div);
                                divContent.appendTo(div);
                                div.appendTo('#graph-performance');

                                //remplissage nbr pourcent
                                total=[];
                                chaine_data = {round:roun,percent:tab[i].percent};
                                total.push(chaine_data);

                                //si dernier tour
                                if (i === (tab.length-1)) {
                                    //creation pourcentage
                                    var divPourc= $('<div id="repart-'+i+'" class="reparts"/>');
                                    divPourc.appendTo(divContent);   
                                    var divSousTitle = $('<div/>');
                                    divSousTitle.text(swim).appendTo(divPourc);
                                    var divTable= $('<table/>');
                                    var th = $('<thead><th>Round</th><th>Perf</th></thead>');
                                    th.appendTo(divTable);
                                    var body = $('<tbody/>');

                                    //calcul de la moyenne
                                    var temp=0;
                                    for (var j=0; j <total.length;j++) {
                                       temp = temp + parseInt(total[j].percent);

                                       var tr = $('<tr/>');                                       
                                       var td1 = $('<td/>');
                                       td1.text(total[j].round).appendTo(tr);
                                       var td2 = $('<td/>');
                                       td2.text(total[j].percent+' %').appendTo(tr);
                                       tr.appendTo(body);                                      
                                    }
                                    body.appendTo(divTable);

                                    var moy = Math.round(temp/total.length*100)/100;
                                    var divText= $('<div class="graph-text-general"/>');
                                    divText.text(moy+' %').appendTo(divPourc);
                                    divTable.appendTo(divPourc);
                                    divPourc.appendTo(divContent);

                                }
                            }
                        }
                    }
                    i++;
                }
            }
        }
    });
    
}
function createPlanification(nageur,course,saison) {
    //vider le graph
    $('#graph-planning').html('');
    
    $.ajax({  
        //On utilise de l'ajax
        type: "POST",  //En post (envoi de données)
        url: '../model/fonctions_request_stat.php', //On va chercher le fichier php
        data: "style=planification&nageur="+nageur+"&course="+course+"&saison="+saison, //On transmet les deux données pour l'exécution de la requête
        success: function(data) { 
            //Si le php renvoie quelque chose
            var tab=$.parseJSON(data);
            
            //verification contenu
            if (!tab)
                $('#pb-planning').text("La recherche n'a pas abouti");
            else {
                $('#pb-planning').text("");

                //remplissage des titres
                var swimmer = $('.search_swimmer option:selected').text();
                var race = $('.search_race option:selected').text();

                $('#planning-title').text('Planification sur la saison');
                if (course === "" && nageur !=="")
                    $('#planning-swim-race').html('<span>'+swimmer+'</span>');
                else if (nageur === "" && course !=="")
                    $('#planning-swim-race').html('<span>'+race+'</span>');
                else if (course !== "" && nageur !=="")
                    $('#planning-swim-race').html('<span>'+swimmer+'</span><span>'+race+'</span>');
                else if (course === "" && nageur ==="")
                    $('#planning-swim-race').html('<span>Tous nageurs</span><span>Toutes nages</span>');
            }

            //simple recuperation de donnees
            Morris.Bar({
                element: 'graph-planning',
                data: tab,
                xkey: 'competition',
                ykeys: ['percent'],
                labels: ['Performance moyenne (%)'],
                barColors: ['#046675']
            });
        }
    });
    
}


