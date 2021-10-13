function afficherVille(tableau){
    console.log(tableau);
    let bodyListe = document.querySelector('#maListeVille');
    let template = document.querySelector('#selecttemplate');
    for (let v of tableau){
        let clone = template.content.cloneNode(true);
        let liste = clone.querySelectorAll("option");
        liste[0].innerHTML = v.nom;
        liste[0].setAttribute('value' ,v.id);
        bodyListe.appendChild(clone);
    }
}
let url = 'http://localhost:8000/villes/api'
fetch(url).then(response => response.json())
.then(tableau => afficherVille(tableau))

let lieux =  [];
let url2 = 'http://localhost:8000/api_lieu'
fetch(url2).then(response => response.json())
    .then(tab =>
    {lieux = tab;
    console.log(lieux);    });


function changeLieu(){
    let option = document.querySelector('#maListeVille');
    let idVille = option.value;
    afficherLieu(idVille);
}


function afficherLieu(idVille){
    let bodyLieu = document.querySelector('#maListeLieu');
    bodyLieu.innerHTML = '';
    let template = document.querySelector('#selecttemplate');
    for (let l of lieux){
        if (l.ville == idVille){
            let clone = template.content.cloneNode(true);
            let liste = clone.querySelector("option");
            liste.innerHTML = l.nom;
            liste.setAttribute('value' ,l.id);
            bodyLieu.appendChild(clone);
        }
    }
}