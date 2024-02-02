import React, { Fragment, useEffect, useRef, useState } from "react";
import { Toaster } from "react-hot-toast";
import Modal from "../../Components/Modal/index.jsx";
import Form from "../Form/index.jsx";
import Delete from "../Delete/index.jsx";

export default function ({ title, projectsUrl, newProjectPath }) {
  const [finalProjets, setFinalProjects] = useState([]);
  const finalTitle = title + (finalProjets.length > 0 ? " (" + finalProjets.length + ")" : "");
  const [project, setProject] = useState(null);
  const [deleteCsrf, setDeleteCsrf] = useState("");
  const [isForm, setIsForm] = useState(false);
  const closeRef = useRef();

  useEffect(() => {
    fetch(projectsUrl, {
      method: "GET",
    })
      .then((res) => res.json())
      .then((res) => {
        if (res?.code === 200) {
          setFinalProjects(res.projects);
        }
      });
  }, []);

  const toggleForm = (type, project = null) => {
    setIsForm(true);

    if (type === "edit" && project) {
      setProject(project);
    } else if (type === "new") {
      setProject(null);
    }
  };
  const handleDelete = (project) => {
    setIsForm(false);
    setDeleteCsrf(project.deleteCsrf);
    setProject(project);
  };

  return (
    <div className="projects">
      <div className="d-flex justify-content-between mb-4">
        <h1>{finalTitle}</h1>
        <button
          type="button"
          className="btn btn-primary h-100 align-self-center"
          data-bs-toggle="modal"
          data-bs-target="#projectModal"
          onClick={() => toggleForm("new")}
        >
          Créer un projet
        </button>
      </div>

      <Toaster />

      <div className="table-responsive">
        <table className="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nom</th>
              <th>Lien repo</th>
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
                  <td className="actions-row">
                    <a href={project.showUrl} className="btn btn-primary btn-sm">
                      <i className="bi bi-eye-fill"></i>
                    </a>

                    <button
                      type="button"
                      className="btn btn-secondary btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#projectModal"
                      onClick={() => toggleForm("edit", project)}
                    >
                      <i className="bi bi-pencil-square"></i>
                    </button>

                    <button
                      type="button"
                      className="btn btn-danger btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#projectModal"
                      onClick={() => handleDelete(project)}
                    >
                      <i className="bi bi-trash-fill"></i>
                    </button>
                  </td>
                </tr>
              </Fragment>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        closeRef={closeRef}
        title={isForm ? (project ? "Modifier un projet" : "Créer un projet") : "Supprimer un projet"}
      >
        {isForm ? (
          <Form
            closeRef={closeRef}
            project={project}
            newProjectPath={newProjectPath}
            finalProjects={finalProjets}
            setFinalProjects={setFinalProjects}
          />
        ) : (
          <Delete closeRef={closeRef} project={project} csrf={deleteCsrf} setFinalProjects={setFinalProjects} />
        )}
      </Modal>
    </div>
  );
}
