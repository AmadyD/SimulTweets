<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<link rel="stylesheet" href="./index.css">
<figure class="highcharts-figure">
    <div id="container"></div>
    <p class="highcharts-description">
        Illustration de la comparaison des hashtags (positif,négatif ou nul) des tweets en fonction des pays.
    </p>
</figure>


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
        text: 'Statitisques des hashtags de tweets en focntion des pays'
    },
    subtitle: {
        text: 'Source: base de données local'
    },
    xAxis: {
        categories: listePays,
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
    var objTweets =[];
    var tweetsPositifs = [];
    var tweetsNegatifs = [];
    var tweetsNeutres = [];

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

            if(count != dataLength){

                dataLength = count;
                listePays = [];
                objTweets =[];

                Object.keys(data).forEach(k=>{
                    var pays = data[k].pays;
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
                });

                 tweetsPositifs = [];
                 tweetsNegatifs = [];
                 tweetsNeutres = [];

                for (let key in objTweets) {
                    var positifs = objTweets[key].positif;
                    var negatifs = objTweets[key].negatif;
                    var neutres = objTweets[key].neutre;
                    tweetsPositifs.push(parseInt(positifs))
                    tweetsNegatifs.push(parseInt(negatifs))
                    tweetsNeutres.push(parseInt(neutres))
                }

                printChart();
            }
            
        }
    };

  charger();

},1000);
}

charger();





</script>