import React, { Fragment } from "react";

export default function ({ title, projects, newPath }) {
  const finalTitle = title + (projects.length > 0 ? " (" + projects.length + ")" : "");

  console.log("projects", projects);

  return (
    <div className="projects">
      <h1>{finalTitle}</h1>

      <table className="table">
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
            <Fragment key={project.id}>
              <tr>
                <td>{project.id}</td>
                <td>{project.name}</td>
                <td>{project.url}</td>
                <td>{project.statut ? "Oui" : "Non"}</td>
                <td>
                  <a href="{{ path('project_show', {'id': project.id}) }}">Voir</a>
                  <a href="/" className="btn btn-primary btn-sm">
                    Voir
                  </a>
                  <button type="button" className="btn btn-secondary btn-sm">
                    Modifier
                  </button>
                </td>
              </tr>
            </Fragment>
          ))}
        </tbody>
      </table>

      <a href={newPath} className="btn btn-primary">
        Créer un projet
      </a>
    </div>
  );
}
