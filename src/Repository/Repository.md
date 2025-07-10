Un repository est un fichier PHP qui permet de faire des recherches  dans la base de données pour une entité donnée.
Il sait comment aller chercher des données dans une table.

Par exemple 
    SessionRepository.php : permet de faire des recherches sur la table session
	StagiaireRepository.php : permet de chercher des stagiaires


Les repository : 

Il hérite de la classe ServiceEntityRepository, ce qui lui permet déjà de faire :
	•	find($id) : trouver un élément par son ID
	•	findAll() : récupérer tous les éléments
	•	findBy([...]) : avec des conditions
	•	findOneBy([...]) ...