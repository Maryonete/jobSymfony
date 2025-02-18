window.importFromURL = function (url) {
  console.log("import " + url);
  let query = url.trim();

  if (query.length < 2) {
    // Évite les requêtes inutiles
    return;
  }

  // Envoi de l'URL dans le corps de la requête POST
  fetch("/admin/importdatas", {
    method: "POST",
    headers: {
      "X-Requested-With": "XMLHttpRequest", // Pour différencier les requêtes AJAX
      "Content-Type": "application/json", // Indique que l'on envoie des données JSON
    },
    body: JSON.stringify({ url: query }), // Envoi de l'URL sous forme de JSON
  })
    .then((response) => response.json()) // Récupère la réponse JSON du serveur
    .then((data) => {
      console.log(data); // Affiche les données JSON récupérées
    })
    .catch((error) => console.error("Erreur:", error));
};
