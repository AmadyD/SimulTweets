<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="./jquery-3.5.1.min.js"></script>
<script src="./bootstrap-4.5.3-dist/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="./index.css">
<link rel="stylesheet" href="./bootstrap-4.5.3-dist/css/bootstrap.min.css">
<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        Illustration de la comparaison des hashtags (positif,négatif ou neutre) des tweets en fonction des pays ou de l'age.
    </p>
</figure>
<div class="highcharts-figure">
<div class="input-group mb-3">
  <div class="input-group-prepend">
    <label class="input-group-text" for="inputGroupSelect01">Filtrer</label>
  </div>
  <select class="custom-select" id="inputGroupSelect01">
    <option value="pays">Par pays</option>
    <option value="age">Par age</option>
  </select>
</div>
</div>



<script>

/*
Fonction pour la création du graphe qui sera exécuter à l'interieur de la fonction charger après récupération des données
*/

const printChart = ()=>{
    Highcharts.chart('container', {
    chart: {
        type: 'column'
    },
    title: {
        text: "Statitisques des hashtags de tweets en focntion des pays ou de l'age des utilisateurs"
    },
    subtitle: {
        text: 'Source: base de données locale'
    },
    xAxis: {
        categories: tabAbcisses,
        crosshair: true
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Nombre de tweets'
        }
    },
    tooltip: {
        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
            '<td style="padding:0"><b>{point.y:.1f} tweets</b></td></tr>',
        footerFormat: '</table>',
        shared: true,
        useHTML: true
    },
    plotOptions: {
        column: {
            pointPadding: 0.2,
            borderWidth: 0
        }
    },
    series: [{
        name: 'Positifs',
        data: tweetsPositifs,
        color: 'green'

    }, {
        name: 'Négatifs',
        data: tweetsNegatifs,
        color: 'red'

    }, {
        name: 'Neutres',
        data: tweetsNeutres,
        color: 'orange'

    }]
});
}


    var dataLength = 0;
    var listePays =[];
    var trancheAges = [];
    var objTweets =[];
    var tweetsParAge = [];
    var tweetsPositifs = [];
    var tweetsNegatifs = [];
    var tweetsNeutres = [];
    var tabAbcisses = [];
    var filtre = "pays";
    var eventFilter = false;

    function filtreSetter(newFiltre){
        this.filtre = newFiltre;
        eventFilter = true;
    }

    var selectElement = document.getElementById('inputGroupSelect01');
/*
Observation du changement d'option au niveau du select
*/
selectElement.addEventListener('change', (event) => {
 filtreSetter(event.target.value);
});

function charger() {
    /*setTimeout permettant de spécifier l'intervalle de temps à laquelle 
    l'appel de la callback sera effectuée (ici 1 seconde) */
setTimeout( function(){
    var ajax = new XMLHttpRequest();
    ajax.open("GET", "data.php", true);
    ajax.responseType = 'text';
    ajax.send();
    // appel de la callback au changement du readyState
    ajax.onreadystatechange = function() {
        if (ajax.readyState == 4 && ajax.status == 200 ) {
            var data = JSON.parse(ajax.responseText);
            var count = Object.keys(data).length;

            // Si un nouvel objet est reçu ou que le setter du filtre est appelé
            if(count != dataLength || eventFilter){

                dataLength = count;
                listePays = [];
                objTweets =[];

                Object.keys(data).forEach(k=>{
                    var pays = data[k].pays;
                    var age = data[k].age;
                    ageConcact = age + " ans";
                    if( !(listePays.includes(pays)) ){
                       listePays.push(pays);
                     /*Addition du nombre de tweets par hashtag en fonction des pays
                     */  
                    donneesTweets = data.reduce(function(sums,entry){
                    if(entry.pays ==pays){
                        sums["pays"] = entry.pays;
                        sums[entry.hashtag] = (sums[entry.hashtag] || 0) + 1;
                    }
                    return sums;
                },{});
                objTweets.push(donneesTweets);
                    }

                    if( !(trancheAges.includes(ageConcact)) ){
                        trancheAges.push(ageConcact);
                   /*Addition du nombre de tweets par hashtag en fonction de l'âge
                     */  
                        donneesTweets = data.reduce(function(sums,entry){
                    if(entry.age == age){
                        sums["age"] = entry.age;
                        sums[entry.hashtag] = (sums[entry.hashtag] || 0) + 1;
                    }
                    return sums;
                },{});
                tweetsParAge.push(donneesTweets);
                    }
                });
                 tweetsPositifs = [];
                 tweetsNegatifs = [];
                 tweetsNeutres = [];
                if(filtre =="pays"){

                    tabAbcisses = listePays;

                    for (let key in objTweets) {
                    var positifs = objTweets[key].positif;
                    var negatifs = objTweets[key].negatif;
                    var neutres = objTweets[key].neutre;
                    tweetsPositifs.push(parseInt(positifs))
                    tweetsNegatifs.push(parseInt(negatifs))
                    tweetsNeutres.push(parseInt(neutres))
                    }
                }else if(filtre =="age"){
                    // trie de l'âge par ordre croissant
                    trancheAges.sort();
                    tabAbcisses = trancheAges;

                    for (let key in tweetsParAge) {
                    var positifs = tweetsParAge[key].positif;
                    var negatifs = tweetsParAge[key].negatif;
                    var neutres = tweetsParAge[key].neutre;
                    tweetsPositifs.push(parseInt(positifs))
                    tweetsNegatifs.push(parseInt(negatifs))
                    tweetsNeutres.push(parseInt(neutres))
                    }
                }

                printChart();
                eventFilter = false;
            }
            
        }
    };

  charger();

},1000);
}


charger();





</script>