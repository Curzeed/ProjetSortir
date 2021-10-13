function afficherLieu(){
    let option = document.querySelector('#choix');
    let idVille = option.getAttribute('value');
    console.log(idVille);

    let url = 'http://localhost:8000/api/lieu'

}


function afficherVille(tableau){
    let bodyListe = document.querySelector('#maListeVille');
    let template = document.querySelector('#selecttemplate');
    for (let v of tableau){
        let clone = template.content.cloneNode(true);
        let liste = clone.querySelectorAll("option");
        liste[0].innerHTML = v.nom;
        liste[0].setAttribute('id','choix');
        liste[0].setAttribute('value' ,v.id);
        bodyListe.appendChild(clone);
    }
}
let url = 'http://localhost:8000/villes/api'
fetch(url).then(response => response.json())
.then(tableau => afficherVille(tableau))