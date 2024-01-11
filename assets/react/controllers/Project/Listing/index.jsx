import React, { Fragment } from "react";
import NewModal from "./NewModal/index.jsx";
import EditModal from "./EditModal/index.jsx";

export default function ({ title, projects, newProjectPath }) {
  const finalTitle = title + (projects.length > 0 ? " (" + projects.length + ")" : "");

  console.log("projects", projects);

  const Spinner = () => (
    <div class="spinner-border spinner-border-sm ms-2" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  );

  return (
    <div className="projects">
      <h1>{finalTitle}</h1>

      <div className="table-responsive">
        <table className="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Lien repo</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {projects.map((project) => (
              <Fragment key={project.id}>
                <tr>
                  <td>{project.id}</td>
                  <td>
                    <a href={project.showUrl}>{project.name}</a>
                  </td>
                  <td>
                    <a href={project.url} target="_blank" rel="noopener noreferrer">
                      {project.url}
                    </a>
                  </td>
                  <td>
                    {project.statut ? (
                      "Terminé"
                    ) : (
                      <>
                        <span>En cours</span>
                        <Spinner />
                      </>
                    )}
                  </td>
                  <td className="actions-row">
                    <button
                      type="button"
                      className="btn btn-secondary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#editProjectModal"
                    >
                      Modifier
                    </button>

                    <button type="button" className="btn btn-secondary btn-sm">
                      Supprimer
                    </button>
                  </td>
                </tr>
              </Fragment>
            ))}
          </tbody>
        </table>
      </div>

      <button type="button" className="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createProjectModal">
        Créer un projet
      </button>

      <NewModal newProjectPath={newProjectPath} />
      <EditModal />
    </div>
  );
}
