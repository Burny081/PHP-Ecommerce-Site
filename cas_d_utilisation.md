# Description des cas d’utilisation

## AUTHENTIFICATION
**But:** Permettre aux utilisateurs (Client, Admin, Super Admin) de s’identifier et d’accéder à leurs interfaces respectives de manière sécurisée.  
**Résumé:** Ce cas d’utilisation décrit le processus de connexion nécessitant un identifiant et un mot de passe valides pour accéder aux fonctionnalités selon le rôle de l’utilisateur.  
**Acteurs:** Client, Admin, Super Admin  
**Précondition:** L’utilisateur est enregistré dans la base de données.  
**Déclenchement:** L’utilisateur clique sur le bouton « Se connecter ».  
**Scénario normal:**
- Le système affiche un formulaire de connexion.
- L’utilisateur saisit son identifiant et mot de passe puis clique sur « Envoyer ».
- Le système vérifie les informations dans la base de données.
- Le système confirme l’accès et redirige l’utilisateur vers son tableau de bord (client, admin, super admin).  
**Scénario alternatif:**  
- Identifiant ou mot de passe invalide → Le système affiche « Données invalides » et renvoie à l’étape 2.  
- L’utilisateur n’existe pas dans la base → Le système affiche « Utilisateur inexistant » et renvoie à l’étape 2.  
**Post-condition succès:** L’utilisateur est connecté et accède à son espace.

## INSCRIPTION
**But:** Permettre à un client de créer un compte pour accéder aux services du site.  
**Résumé:** Ce cas décrit le processus de création d’un compte client avec saisie et validation des informations.  
**Acteurs:** Client  
**Précondition:** Le client n’a pas de compte dans la base.  
**Déclenchement:** Le client clique sur « S’inscrire ».  
**Scénario normal:**
- Le système affiche un formulaire d’inscription.
- Le client remplit les champs et clique sur « Enregistrer ».
- Le système vérifie l’unicité de l’email.
- Le système enregistre les données.
- Message de confirmation : « Inscription réussie ».  
**Scénario alternatif:**  
- Champs obligatoires vides → Message : « Veuillez remplir tous les champs ». Retour étape 2.  
- Email déjà utilisé → Message : « Cet email est déjà pris ». Retour étape 2.  
**Post-condition succès:** Le client dispose d’un compte et peut se connecter.

## CONSULTER LE CATALOGUE
**But:** Permettre au client de visualiser les produits disponibles.  
**Résumé:** Ce cas décrit la consultation du catalogue produit depuis l’interface.  
**Acteurs:** Client  
**Précondition:** Le client est connecté.  
**Déclenchement:** Le client clique sur « Catalogue ».  
**Scénario normal:**
- Le système interroge la base de données.
- Le système affiche la liste des produits avec détails.  
**Scénario alternatif:**  
- Aucun produit disponible → Message « Catalogue vide ».  
**Post-condition succès:** Le client accède au catalogue des produits.

## AJOUTER UN PRODUIT AU PANIER
**But:** Permettre au client de sélectionner un produit et l’ajouter au panier.  
**Résumé:** Ce cas décrit l’action d’ajout d’un produit.  
**Acteurs:** Client  
**Précondition:** Le client est connecté.  
**Déclenchement:** Le client clique sur « Ajouter au panier ».  
**Scénario normal:**
- Le client sélectionne un produit.
- Le système vérifie le stock.
- Le produit est ajouté au panier.
- Message de confirmation : « Produit ajouté ».  
**Scénario alternatif:**  
- Stock insuffisant → Message : « Produit indisponible ».  
**Post-condition succès:** Le produit est ajouté au panier du client.

## SUPPRIMER UN PRODUIT DU PANIER
**But:** Permettre au client de retirer un produit de son panier.  
**Résumé:** Ce cas décrit l’action de suppression d’un produit du panier.  
**Acteurs:** Client  
**Précondition:** Le panier contient au moins un produit.  
**Déclenchement:** Le client clique sur « Supprimer du panier ».  
**Scénario normal:**
- Le client sélectionne un produit.
- Le système retire le produit.
- Message : « Produit supprimé ».  
**Scénario alternatif:**  
- Produit inexistant → Message : « Produit non trouvé dans le panier ».  
**Post-condition succès:** Le panier est mis à jour.

## VALIDER LE PANIER
**But:** Permettre au client de confirmer son panier avant de passer commande.  
**Résumé:** Ce cas décrit la validation d’un panier avant sa conversion en commande.  
**Acteurs:** Client  
**Précondition:** Le panier contient au moins un produit.  
**Déclenchement:** Le client clique sur « Valider ».  
**Scénario normal:**
- Le système vérifie le panier.
- Le système confirme la validation.  
**Scénario alternatif:**  
- Panier vide → Message : « Votre panier est vide ».  
**Post-condition succès:** Le panier est validé et prêt à être commandé.

## PASSER COMMANDE
**But:** Permettre au client de transformer un panier validé en commande.  
**Résumé:** Ce cas décrit la création d’une commande et son paiement.  
**Acteurs:** Client  
**Précondition:** Le panier a été validé.  
**Déclenchement:** Le client clique sur « Commander ».  
**Scénario normal:**
- Le système génère une commande.
- Le client choisit le mode de paiement.
- Le système appelle le service de paiement.
- Paiement confirmé.
- Le système met à jour le statut de la commande.
- Message : « Commande confirmée ».  
**Scénario alternatif:**  
- Paiement refusé → Message « Paiement échoué », retour à l’étape 2.  
**Post-condition succès:** Commande enregistrée et statut confirmé.

## CONSULTER MES COMMANDES
**But:** Permettre au client de consulter l’historique de ses commandes.  
**Résumé:** Ce cas décrit la consultation des commandes effectuées par le client.  
**Acteurs:** Client  
**Précondition:** Le client est connecté.  
**Déclenchement:** Le client clique sur « Mes commandes ».  
**Scénario normal:**
- Le système interroge la base.
- La liste des commandes est affichée.  
**Scénario alternatif:**  
- Aucune commande → Message « Vous n’avez aucune commande ».  
**Post-condition succès:** Les commandes sont affichées.

## ANNULER UNE COMMANDE
**But:** Permettre au client d’annuler une commande en attente.  
**Résumé:** Ce cas décrit l’action d’annulation d’une commande.  
**Acteurs:** Client  
**Précondition:** La commande est encore annulable.  
**Déclenchement:** Le client clique sur « Annuler ».  
**Scénario normal:**
- Le système vérifie le statut.
- Le système annule la commande.
- Message : « Commande annulée ».  
**Scénario alternatif:**  
- Commande déjà expédiée → Message « Annulation impossible ».  
**Post-condition succès:** La commande est annulée.

## ENVOYER UN MESSAGE
**But:** Permettre à un utilisateur d’envoyer un message interne.  
**Résumé:** Ce cas décrit l’envoi d’un message via la messagerie interne.  
**Acteurs:** Client, Admin, Super Admin  
**Précondition:** L’utilisateur est connecté.  
**Déclenchement:** L’utilisateur clique sur « Nouveau message ».  
**Scénario normal:**
- Le système affiche un formulaire.
- L’utilisateur saisit son message et clique sur « Envoyer ».
- Le système enregistre et notifie le destinataire.
- Message : « Votre message a été envoyé ».  
**Scénario alternatif:**  
- Message vide → Message d’erreur « Champ obligatoire non rempli ».  
**Post-condition succès:** Le message est envoyé et stocké.

## CONSULTER MES MESSAGES
**But:** Permettre à un utilisateur de consulter ses messages reçus et envoyés.  
**Résumé:** Ce cas décrit la consultation de la messagerie interne.  
**Acteurs:** Client, Admin, Super Admin  
**Précondition:** L’utilisateur est connecté.  
**Déclenchement:** L’utilisateur clique sur « Messagerie ».  
**Scénario normal:**
- Le système affiche la liste des messages.
- L’utilisateur sélectionne un message.
- Le système affiche le contenu détaillé.  
**Post-condition succès:** L’utilisateur accède à sa conversation.

## AJOUTER UN PRODUIT
**But:** Permettre à l’admin d’ajouter un produit au catalogue.  
**Résumé:** Ce cas décrit l’ajout d’un nouveau produit par l’admin.  
**Acteurs:** Admin  
**Précondition:** L’admin est connecté.  
**Déclenchement:** L’admin clique sur « Ajouter produit ».  
**Scénario normal:**
- Le système affiche un formulaire.
- L’admin saisit les données et clique sur « Enregistrer ».
- Le produit est ajouté.
- Message : « Produit ajouté ».  
**Scénario alternatif:**  
- Champs obligatoires vides → Message d’erreur.  
**Post-condition succès:** Le produit est enregistré.

## MODIFIER UN PRODUIT
**But:** Permettre à l’admin de modifier un produit existant.  
**Résumé:** Ce cas décrit la modification d’un produit par l’admin.  
**Acteurs:** Admin  
**Précondition:** L’admin est connecté.  
**Déclenchement:** L’admin clique sur « Modifier produit ».  
**Scénario normal:**
- Le système affiche le produit.
- L’admin modifie les informations et clique sur « Enregistrer ».
- Le produit est mis à jour.  
**Scénario alternatif:**  
- Données invalides → Message d’erreur.  
**Post-condition succès:** Produit mis à jour.

## SUPPRIMER UN PRODUIT
**But:** Permettre à l’admin de supprimer un produit.  
**Résumé:** Ce cas décrit la suppression d’un produit par l’admin.  
**Acteurs:** Admin  
**Précondition:** L’admin est connecté.  
**Déclenchement:** L’admin clique sur « Supprimer produit ».  
**Scénario normal:**
- Le système demande confirmation.
- L’admin valide.
- Le produit est supprimé.  
**Scénario alternatif:**  
- L’admin annule la suppression → Suppression annulée.  
**Post-condition succès:** Le produit est supprimé.

## AJOUTER UN UTILISATEUR
**But:** Permettre au super admin de créer un compte utilisateur.  
**Résumé:** Ce cas décrit la création d’un utilisateur par le super admin.  
**Acteurs:** Super Admin  
**Précondition:** Le super admin est connecté.  
**Déclenchement:** Le super admin clique sur « Ajouter utilisateur ».  
**Scénario normal:**
- Le système affiche un formulaire.
- Le super admin saisit les informations et clique sur « Enregistrer ».
- L’utilisateur est ajouté.  
**Scénario alternatif:**  
- Identifiant déjà existant → Message d’erreur.  
**Post-condition succès:** Nouvel utilisateur enregistré.

## MODIFIER UN UTILISATEUR
**But:** Permettre au super admin de modifier un utilisateur existant.  
**Résumé:** Ce cas décrit la mise à jour d’un compte utilisateur.  
**Acteurs:** Super Admin  
**Précondition:** Le super admin est connecté.  
**Déclenchement:** Le super admin clique sur « Modifier utilisateur ».  
**Scénario normal:**
- Le système affiche la fiche utilisateur.
- Le super admin modifie les informations et enregistre.
- Mise à jour confirmée.  
**Scénario alternatif:**  
- Données invalides → Message d’erreur.  
**Post-condition succès:** Utilisateur mis à jour.

## SUPPRIMER UN UTILISATEUR
**But:** Permettre au super admin de supprimer un utilisateur.  
**Résumé:** Ce cas décrit la suppression d’un utilisateur par le super admin.  
**Acteurs:** Super Admin  
**Précondition:** Le super admin est connecté.  
**Déclenchement:** Le super admin clique sur « Supprimer utilisateur ».  
**Scénario normal:**
- Le système demande confirmation.
- Le super admin valide.
- L’utilisateur est supprimé.  
**Scénario alternatif:**  
- Le super admin annule → Suppression annulée.  
**Post-condition succès:** Utilisateur supprimé.
