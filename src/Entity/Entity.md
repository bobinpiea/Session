Le dossier src/Entity/ contient toutes les entités PHP de mon application.
Une entité, est une classe qui représente une table de ma base de données. Donc une entité par classe

Exemple : 

table session dans ma base de données
Symfony aura donc une classe Session dans le fichier src/Entity/Session.php.

Cette classe contient :
	•	des propriétés (correspondent aux colonnes)
	•	des relations (avec d’autres entités)
	•	des annotations Doctrine ( me renseigner dessus)
	•	des getters/setters pour accéder aux données

Symfony utilise ces entités :
	•	pour générer la base de données (migrations)
	•	pour créer des formulaires liés à ces entités
	•	pour récupérer et manipuler les données avec Doctrine (ORM)
