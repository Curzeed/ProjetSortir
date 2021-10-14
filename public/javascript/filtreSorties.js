let url = 'http://localhost:8000/api/sortie';
let urlCampus = 'http://localhost:8000/campus/api';


fetch(url).then(response => response.json())
.then(tableau => afficherSortie(tableau));
function afficherSortie(tableau){
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');
    let urlModif = 'http://localhost:8000/sorties/detail/';
    let urlDesister = 'http://localhost:8000/sorties/desister/';
    let urlInscription = 'http://localhost:8000/sorties/inscription/';
    for (let s of tableau){
        let urlInscription2 = urlInscription+s.id;
        let urlDesister2 = urlDesister+s.id;
        let urlModif2 = urlModif+s.id;
        let clone = template.content.cloneNode(true);
        let tabTd = clone.querySelectorAll('td');

        tabTd[0].innerHTML = s.nom;
        tabTd[1].innerHTML = s.dateHeureDebut.substr(0, 9);
        tabTd[2].innerHTML = s.dateLimiteInscription.substr(0,9);
        tabTd[3].innerHTML = s.nbParticipantsInscrits + "/" + s.nbInscriptionsMax;
        tabTd[4].innerHTML = s.etat;
        tabTd[6].innerHTML = s.organisateur;
        tabTd[9].querySelector('a').setAttribute('href',urlModif2);
        if (s.userInscrit == false){
            tabTd[5].querySelector('i').setAttribute('hidden', '');

        }if(s.userInscrit == true){
            tabTd[10].querySelector('a').setAttribute('hidden','');
        }
        tabTd[10].querySelector('a').setAttribute('href',urlInscription2)
        tabTd[7].querySelector('a').setAttribute('href', urlDesister2)
        body.appendChild(clone);
    }
}
fetch(urlCampus).then(response => response.json())
    .then(campus =>{
        afficherCampus(campus);
    });

function afficherCampus(campus){
//console.log(campus);
    let template = document.querySelector('#templateCampus');

    let body = document.querySelector('#listeCampus');

    for(let c of campus){

        //let clone = template.cloneNode(true);

        let clone = template.content.cloneNode(true);
        let option = clone.querySelector('option');
        option.innerHTML = c.nom;
        option.setAttribute('value',c.id);
        body.appendChild(clone);
    }
}