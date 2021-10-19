let url = 'http://localhost:8000/campus/api';
let tab = [];



fetch(url).then(response => response.json())
    .then(tableau => {
        afficherCampus(tableau);
        tab = tableau;
        console.log(tab);
    });

function afficherCampus(tableau) {
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');
    let urlModif ='http://localhost:8000/campus/modifier/'

    let urlSup = 'http://localhost:8000/campus/supprimer/'

    body.innerHTML = '';

    for (let v of tableau) {
        let urlModif2 = urlModif+v.id;
        let urlSup2 = urlSup+v.id
        let clone = template.content.cloneNode(true);
        let tabTd = clone.querySelectorAll('td');
        tabTd[0].innerHTML = v.nom;
        tabTd[1].querySelector('a').setAttribute('href',urlModif2);
        tabTd[2].querySelector('a').setAttribute('href',urlSup2);
        document.querySelector('#campus_nom').setAttribute('placeholder','Campus');
        body.appendChild(clone);

    }
}

function filtre1(){
    let tab2 =[];
    tab2 = filtreNomCampus(tab);
    afficherCampus(tab2);
    return tab2;
}

function filtreNomCampus(tab) {
    let tab2 = [];

    let nom = document.querySelector('#campus').value;
    if (nom.length >0) {
        for (let v of tab) {
            // Vérifier que le campus contient un nom
            if (v.nom.indexOf(nom) !== -1) {
                tab2.push(v);
            }
        }
    }else{
        tab2 = tab; // pas de filtre
    }
    return tab2;
}

