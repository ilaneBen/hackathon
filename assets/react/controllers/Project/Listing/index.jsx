import React, { Fragment, useState } from "react";
import Modal from "../../Components/Modal/index.jsx";
import Form from "../Form/index.jsx";

export default function ({ title, projects, newProjectPath }) {
  const finalTitle = title + (projects.length > 0 ? " (" + projects.length + ")" : "");
  const [finalProjets, setFinalProjects] = useState(projects);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [projectToEdit, setProjectToEdit] = useState(null);

  const Spinner = () => (
    <div className="spinner-border spinner-border-sm ms-2" role="status">
      <span className="visually-hidden">Loading...</span>
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
            {finalProjets.map((project) => (
              <Fragment key={project.id}>
                <tr>
                  <td>{project.id}</td>
                  <td>{project.name}</td>
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
                    <a href={project.showUrl} className="btn btn-primary btn-sm">
                      <i className="bi bi-eye-fill"></i>
                    </a>

                    <button
                      type="button"
                      className="btn btn-secondary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#editProjectModal"
                    >
                      <i className="bi bi-pencil-square"></i>
                    </button>

                    <button type="button" className="btn btn-danger btn-sm">
                      <i className="bi bi-trash-fill"></i>
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

      <Modal title={projectToEdit ? "Modifier un projet" : "Créer un projet"}>
        <Form project={projectToEdit} newProjectPath={newProjectPath} />
      </Modal>
    </div>
  );
}
