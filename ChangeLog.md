ChangeLog

v1.1
- Ajout de la propriété `life` (vie) à l'entité `Character` (colonne `gls_life`).
- Mise à jour du formulaire `CharacterType` et des fixtures (`AppFixtures`) pour inclure la propriété `life`.
- Création et application d'une migration pour synchroniser la base de données PostgreSQL.
- Création d'un nouveau endpoint `GET /characters/life/{level}` pour lister les personnages ayant un niveau de vie supérieur ou égal à une valeur donnée.
- Implémentation des requêtes spécifiques dans `CharacterRepository` et de la pagination dans `CharacterService`.
- Mise à jour des règles de sécurité (`security.yaml`) pour autoriser l'accès public en lecture (`GET`) aux routes `/characters`.


v0.5
- Mise en place de l'utilisation de l'API

v1.0
- Core files