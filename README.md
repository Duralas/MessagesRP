# Dùralas
[Dùralas](https://www.lemondededuralas.org/) est un forum français de rôle-play dans un univers médiéval-fantastique. 
Le principe est d'écrire des récits mettant en scène son propre personnage afin d'interagir avec d'autres joueurs.

## Le Phénix Enchaîné

Le [Phénix Enchaîné](https://www.lemondededuralas.org/f130-le-phenix-enchaine) est la gazette du forum contenant 
des informations à la fois IRL pour les joueurs mais aussi RP, notamment sur des événments qui ont eu lieu 
ou des nouveautés et surprises sortantes ou à venir.

Parmi les articles, il y a une catégorie statistiques qui fait un recensement sur des variables RP (nombre de races, 
de classes, de récolteurs/artisans par métier, de membres de factions ou guildes par structure, etc.) et sur les 
messages RP rédigés.


# Messages RP

Ce projet réalisé en Symfony 5 a pour but de facilement recenser les messages RP selon un format spécifique :
* Période réelle (correspondant à la couverture entre deux numéros du Phénix).
* Région et zone de Dùralas.
* Mois réel pendant lequel a été posté le RP
* Personnage auteur du message.

À partir de ces données, il sera possible de définir des statistiques :
* Meilleurs posteurs de la période / par mois.
* Région la plus active de la période / par mois.
* Faction la plus active.
* etc.

## Réalisation

Plusieurs étapes sont nécessaires pour utiliser l'application mais surtout pour la rendre la plus complète possible selon les besoins :
- [x] Entités
   - [ ] CRUD
- [ ] Formulaire de recensement
   - [ ] Définir la période active
   - [ ] Définir des variables en cache pour économiser des clics
- [ ] Page de consultation de résultats
   - [ ] Définir tous les rapports à consulter
   - [ ] Fournir le template pour le Phénix

À savoir que pour être fonctionnelle, l'application a besoin des entités et du formulaire de recensement dans leur forme la plus triviale.
