import React, { useRef, useState } from "react";
import Modal from "../../Components/Modal/index.jsx";
import Form from "../../Rapport/Form";
import clsx from "clsx";
import "./style.scss";
import DeleteForm from "../../Components/DeleteForm/index.jsx";

const ProjectDetails = ({ project }) => {
  let dernierRapport = null;
  const [rapports, setRapports] = useState(project.rapport);
  const [rapport, setRapport] = useState(null);
  const [deleteCsrf, setDeleteCsrf] = useState("");
  const [isForm, setIsForm] = useState(false);
  const [isCollapse, setIsCollapse] = useState(false);
  const closeRef = useRef();

  const handleDelete = (rapport) => {
    setIsForm(false);
    setDeleteCsrf(rapport.deleteCsrf);
    setRapport(rapport);
  };

  const chooseTest = () => {
    setIsForm(true);
  };

  if (project.rapport && project.rapport.length !== 0) {
    // Triez les rapports par ID pour obtenir le dernier
    const rapportsTriés = [...project.rapport].sort((a, b) => b.id - a.id);
    dernierRapport = rapportsTriés[0];
  }

  return (
    <div className="m-2">
      <div className="d-flex">
        <h1>Rapports du projet {project.name}</h1>
        <img className="img-report" src="/img/monitor.png" />
      </div>
      <h2 className="bg-success"></h2>
      <table className="table table-striped-columns">
        <tbody>
          <tr>
            <th>URL</th>
            <td>{project.url}</td>
          </tr>
        </tbody>
      </table>
      {dernierRapport && (
        <div className="d-flex justify-content-evenly mt-5">
          <p>Rapport n: {dernierRapport.id}</p>
          <a className="btn btn-light" href={dernierRapport.showUrl}>
            <span className="label">Voir le dernier rapport</span>
          </a>
        </div>
      )}
      {/* Rendu du tableau */}
      {rapports && rapports.length !== 0 ? (
        <div className="mt-5">
          <a
            href="#collapse-rapport-historique"
            data-bs-toggle="collapse"
            role="button"
            aria-expanded="false"
            aria-controls="collapse-rapport-historique"
            onClick={() => setIsCollapse(!isCollapse)}
          >
            <div className="d-flex div-rapport justify-content-between">
              <h2>Rapports du projet</h2>
              <i
                className={clsx("bi bi-chevron-down chevron-rapport", isCollapse && "collapsed")}
                styles="font-size: 2rem;"
              ></i>
            </div>
          </a>
          <div className="collapse" id="collapse-rapport-historique">
            <table className="table table-striped ">
              <thead>
                <tr>
                  <th styles="max-width: 5%; min-width: 5%;">ID</th>
                  <th styles="max-width: 95%; min-width: 95%;">Actions</th>
                </tr>
              </thead>
              <tbody>
                {rapports.map((rapport) => (
                  <tr key={rapport.id}>
                    <td styles="max-width: 5%; min-width: 5%;">{rapport.id}</td>
                    <td styles="max-width: 95%; min-width: 95%;">
                      <div className="d-flex">
                        <div className="d-flex align-items-center">
                          <a href={rapport.showUrl} className="button">
                            <svg
                              xmlns="http://www.w3.org/2000/svg"
                              width="20"
                              viewBox="0 0 20 20"
                              height="20"
                              fill="none"
                              className="svg-icon"
                            >
                              <g strokeWidth="1.5" strokeLinecap="round" stroke="#5d41de">
                                <circle r="2.5" cy="10" cx="10"></circle>
                                <path
                                  fillRule="evenodd"
                                  d="m8.39079 2.80235c.53842-1.51424 2.67991-1.51424 3.21831-.00001.3392.95358 1.4284 1.40477 2.3425.97027 1.4514-.68995 2.9657.82427 2.2758 2.27575-.4345.91407.0166 2.00334.9702 2.34248 1.5143.53842 1.5143 2.67996 0 3.21836-.9536.3391-1.4047 1.4284-.9702 2.3425.6899 1.4514-.8244 2.9656-2.2758 2.2757-.9141-.4345-2.0033.0167-2.3425.9703-.5384 1.5142-2.67989 1.5142-3.21831 0-.33914-.9536-1.4284-1.4048-2.34247-.9703-1.45148.6899-2.96571-.8243-2.27575-2.2757.43449-.9141-.01669-2.0034-.97028-2.3425-1.51422-.5384-1.51422-2.67994.00001-3.21836.95358-.33914 1.40476-1.42841.97027-2.34248-.68996-1.45148.82427-2.9657 2.27575-2.27575.91407.4345 2.00333-.01669 2.34247-.97026z"
                                  clipRule="evenodd"
                                ></path>
                              </g>
                            </svg>
                            <span className="lable">Voir le rapport</span>
                          </a>
                        </div>
                        <form className="form-show" method="POST">
                          <button
                            type="button"
                            className="button-delete"
                            data-bs-toggle="modal"
                            data-bs-target="#projectModal"
                            onClick={() => handleDelete(rapport)}
                          >
                            <svg viewBox="0 0 448 512" className="svgIcon">
                              <path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"></path>
                            </svg>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      ) : (
        <p>Aucun rapport disponible pour ce projet.</p>
      )}

      <div className="mb-2 mt-3 d-flex">
        <a className="button-repport" href={project.indexUrl}>
          Retour à la liste
        </a>

        <button className="button-repport2" data-bs-toggle="modal" data-bs-target="#projectModal" onClick={chooseTest}>
          Lancer le Test
        </button>
      </div>

      <Modal closeRef={closeRef} title={isForm ? "Créer un rapport" : "Supprimer un rapport"}>
        {isForm ? (
          <Form closeRef={closeRef} rapports={rapports} project={project} />
        ) : (
          <DeleteForm
            closeRef={closeRef}
            elementToDelete={rapport}
            csrf={deleteCsrf}
            setState={setRapports}
            buttonText="Supprimer le rapport"
            deleteUrl={rapport?.deleteUrl}
          />
        )}
      </Modal>
    </div>
  );
};

export default ProjectDetails;
