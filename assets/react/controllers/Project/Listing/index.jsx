import React from "react";

export default function ({ title, projects, newPath }) {
  const finalTitle = title + (projects.length > 0 ? " (" + projects.length + ")" : "");

  return (
    <div className="projects">
      <h1>{finalTitle}</h1>

      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>URL</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {projects.map((project) => (
            <>
              <tr>
                <td>{project.id}</td>
                <td>{project.name}</td>
                <td>{project.url}</td>
                <td>{project.statut ? "Oui" : "Non"}</td>
                <td>
                  <a href="{{ path('project_show', {'id': project.id}) }}">Voir</a>
                  <a href="/" class="btn btn-primary btn-sm">
                    Voir
                  </a>
                  <button type="button" class="btn btn-secondary btn-sm">
                    Modifier
                  </button>
                </td>
              </tr>
            </>
          ))}
        </tbody>
      </table>

      <a href={newPath} className="btn btn-primary">
        Créer un projet
      </a>
    </div>
  );
}
