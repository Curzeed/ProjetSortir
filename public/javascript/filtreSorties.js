let url = 'http://localhost:8000/api/sortie';
let urlCampus = 'http://localhost:8000/campus/api';
let tabGlobal = [];

const dateActuelle= new Date(Date.now());


fetch(url).then(response => response.json())
.then(tableau => {
    afficherSortie(tableau);
    tabGlobal = tableau;
    console.log(tabGlobal);
});
function afficherSortie(tableau){
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');
    let urlModif = 'http://localhost:8000/sorties/detail/';
    let urlDesister = 'http://localhost:8000/sorties/desister/';
    let urlInscription = 'http://localhost:8000/sorties/inscription/';
    let urlModifSortie = 'http://localhost:8000/sorties/modifier/';
    let urlAnnulerSortie = 'http://localhost:8000/sorties/annuler/';
    let urlDetailsProfil = 'http://localhost:8000/participants/infos/';
    body.innerHTML = '';
    for (let s of tableau){
        let urlInscription2 = urlInscription+s.id;
        let urlDesister2 = urlDesister+s.id;
        let urlModif2 = urlModif+s.id;
        let urlModifSortie2 = urlModifSortie+s.id;
        let urlAnnulerSortie2 = urlAnnulerSortie+s.id;
        let dateHeureDebut = new Date(s.dateHeureDebut);
        let dateLimiteInscription = new Date(s.dateLimiteInscription);
        let date = new Date(Date.now());
        let clone = template.content.cloneNode(true);
        let tabTd = clone.querySelectorAll('td');
        let urlDetailsProfil2 = urlDetailsProfil+s.userIdentifier;

        if(dateHeureDebut.getMonth() != date.getMonth()-1){
            tabTd[0].innerHTML = s.nom;
            tabTd[1].innerHTML = dateHeureDebut.toLocaleDateString("fr-FR");
            tabTd[2].innerHTML = dateLimiteInscription.toLocaleDateString("fr-FR");
            tabTd[3].innerHTML = s.nbParticipantsInscrits + "/" + s.nbInscriptionsMax;
            tabTd[4].innerHTML = s.etat;
            tabTd[6].querySelector('a').innerHTML = s.organisateur;
            tabTd[6].querySelector('a').setAttribute('href',urlDetailsProfil2);
            tabTd[6].querySelector('a').setAttribute('class','Lienorganisateur');
            tabTd[9].querySelector('a').setAttribute('href',urlModif2);
            tabTd[7].querySelector('a').setAttribute('href', urlDesister2);
            tabTd[10].querySelector('a').setAttribute('href',urlInscription2);
            tabTd[8].querySelector('a').setAttribute('href',urlModifSortie2);
            tabTd[11].querySelector('a').setAttribute('href',urlAnnulerSortie2);
            if (s.userInscrit === false){
                tabTd[5].querySelector('i').setAttribute('hidden', '');
                tabTd[7].querySelector('a').setAttribute('hidden','');
            }if(s.userInscrit === true) {
                tabTd[10].querySelector('a').setAttribute('hidden', '');
            }
            let nouvelledate = new Date(s.dateLimiteInscription);
            if(s.etat == 'Pass??e' | s.etat == 'Clotur??e' | s.etat == 'Annul??e'){
                tabTd[10].querySelector('a').setAttribute('hidden', '') ;
            }
            if(s.EstOrganisateur === false && s.rolesUser.includes('ROLE_ADMIN') === false){
                tabTd[8].querySelector('a').setAttribute('hidden','');
                tabTd[11].querySelector('a').setAttribute('hidden','');
            }
            body.appendChild(clone);
        }

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
function isValidDate(d) {
    return d instanceof Date && !isNaN(d);
}

function filtre(){
    let tab2 =tabGlobal;
    tab2 = filtreNom(tab2);

    tab2 = filtreCampus(tab2);
    tab2 = filtreSortiesOrganisateur(tab2);
    tab2 = filtreSortiesInscrit(tab2);
    tab2 = filtreNoInscrit(tab2);
    tab2 = filtreSortiesPassees(tab2);

    tab2 = filtreParDate(tab2);
    //console.log(tab2);
    afficherSortie(tab2);
}
function filtreNom(tableau){
    let tab2 = [];
    let nom = document.querySelector('#sortie').value;
    if (nom.length >0) {
        for (let s of tableau) {
            // V??rifier que la sortie contient un nom
            if (s.nom.indexOf(nom) !== -1) {
                tab2.push(s);
            }
        }
    }else{
        tab2 = tableau; // pas de filtre
    }
    return tab2;
}

function filtreCampus(tableau){
    let tab2 =[];
    let campus = document.querySelector('#listeCampus').value;
    if (campus !=0){
        for (let s of tableau) {
            if (s.idcampus == campus){
                tab2.push(s);
            }
        }
    }else{
        tab2 = tableau;
    }
    return tab2;
}
function filtreSortiesOrganisateur(tableau){
    let tab2 = [];
    let checkboxOrganisateur = document.querySelector('#checkboxOrganisateur');
    if (checkboxOrganisateur.checked){
        for (let s of tableau){
            if (s.EstOrganisateur === true){
                tab2.push(s);
            }
        }
    }else{
        tab2 = tableau;
    }
    return tab2;
}
function filtreSortiesInscrit(tableau){
    let tab2 = [];
    let checkboxInscrit = document.querySelector('#checkboxInscrit');
    if (checkboxInscrit.checked) {
        for (let s of tableau){
            if (s.userInscrit == true){
                tab2.push(s);
            }
        }

    }else{
        tab2 = tableau;
    }
    return tab2;
}
function filtreNoInscrit(tableau){
    let tab2 = [];
    let checkboxNoInscrit = document.querySelector('#checkboxNoInscrit');
    if (checkboxNoInscrit.checked){
        for (let s of tableau){
            if(s.userInscrit == false){
                tab2.push(s);
            }
        }
    }else{
        tab2 =tableau;
    }
    return tab2;
}
function filtreSortiesPassees(tableau){
    let tab2 = [];
    let checkboxSortiesPassees = document.querySelector('#checkboxSortiesPassees');

    if (checkboxSortiesPassees.checked){
        for(let s of tableau){
            if(s.etat == "Pass??e"){
                tab2.push(s)
            }
        }
    }else{
        tab2 = tableau;
    }
    return tab2;
}

function filtreParDate(tableau) {
    let tab2 = [];
    let dateMin = document.querySelector('#dateMin').value;
    let dateMax = document.querySelector('#dateMax').value;
    if ((dateMin.length != 0) && (dateMax.length !=0)){
        dateMin = new Date(dateMin);
        dateMax = new Date(dateMax);
        for(let s of tableau){
            let dateDebut = new Date(s.dateHeureDebut);
           // let dateLimite = new Date(s.dateLimiteInscription);
            if( dateDebut >= dateMin){
                if(dateDebut <= dateMax){
                    tab2.push(s);
                }
            }

        }
    }else{
        tab2 =tableau;
    }
    return tab2;
}

var options = {
    animation : true,
    delay : 2000
};
function toasty(){
    var toast = document.querySelector('#liveToastBtn');

    var toastELement = new bootstrap.Toast(toast, options);

    toastELement.show();
}