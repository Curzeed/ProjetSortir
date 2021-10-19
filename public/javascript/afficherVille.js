let url = 'http://localhost:8000/villes/api';
let tab = [];



fetch(url).then(response => response.json())
    .then(tableau => {
        afficherVille(tableau);
        tab = tableau;
        console.log(tab);
    });

function afficherVille(tableau) {
    let body = document.querySelector('#myTbody');
    let template = document.querySelector('#ligne');
    //let urlModif ...
    //let urlSup ...

    body.innerHTML = '';

    for (let v of tableau) {
        // let urlModif2 = urlModif+v.id;
        // let urlSup2 = urlSup+v.id
        let clone = template.content.cloneNode(true);
        let tabTd = clone.querySelectorAll('td');
        tabTd[0].innerHTML = v.nom;
        tabTd[1].innerHTML = v.codePostal;
        //tabTd[2].querySelector('a').setAttribute('href',urlModif2)
        //tabTd[3].querySelector('a').setAttribute('href',urlSup2)

        body.appendChild(clone);

    }
}

    function filtre1(){
        let tab2 =[];
        tab2 = filtreNomVille(tab);
       // tab2 = filtrecodePostal(tab2);
        afficherVille(tab2);
        return tab2;
    }

    function filtreNomVille(tab) {
        let tab2 = [];

        let nom = document.querySelector('#ville').value;
        if (nom.length >0) {
            for (let v of tab) {
                // Vérifier que la ville contient un nom
                if (v.nom.indexOf(nom) !== -1) {
                    tab2.push(v);
                }
            }
        }else{
            tab2 = tab; // pas de filtre
        }
        return tab2;
    }
console.log('test')
