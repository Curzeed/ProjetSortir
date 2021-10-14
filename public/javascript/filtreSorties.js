let url = 'http://localhost:8000/api/sortie';
let urlCampus = 'http://localhost:8000/campus/api';


fetch(url).then(response => response.json())
.then(tableau => afficherSortie(tableau));

fetch(url2).then(response => response.json())
    .then(tableau => afficherCampus(tableau));

function afficherSortie(tableau){
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');
    let urlImage = "{{ asset('img/icons8-coche-48.png') }}" ;
    let urlModif = 'http://localhost:8000/sorties/detail/';
    for (let s of tableau){
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
            tabTd[5].querySelector('img').setAttribute('hidden', '');
        }
        body.appendChild(clone);
    }
}
function afficherCampus(tableau){
    let template = document.querySelector('#templateCampus');
    let body = document.querySelector('#listeCampus')
    for(let c of tableau){
        let clone = template.cloneNode(true);
        let option = clone.querySelectorAll('option');

        option
    }
}
