let url = 'http://localhost:8000/api/sortie';

fetch(url).then(response => response.json())
.then(tableau => afficherSortie(tableau));

function afficherSortie(tableau){
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');

    for (let s of tableau){
        let clone = template.content.cloneNode(true);
        let tabTd = clone.querySelectorAll('td');
        let date = s.dateHeureDebut
        tabTd[0].innerHTML = s.nom;
        tabTd[1].innerHTML = s.dateHeureDebut.substr(0, 9);

    }
}