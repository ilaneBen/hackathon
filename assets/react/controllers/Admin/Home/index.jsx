import React, { Fragment, useEffect, useState } from "react";
import { Bar } from 'react-chartjs-2';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    Title,
    Tooltip,
    Legend,
} from 'chart.js';

export default function ({ message, dashboardUrl, usersUrl }) {

    const [data, setData] = useState([]);
    const [chartsOpt, setChartsOpt] = useState({});
    const now = (new Date()).getFullYear();
    const [chartDate, setChartDate] = useState(now);
    const [chartTabs, setChartTabs] = useState([]);
    const [chartTab, setChartTab] = useState("");
    const [copyOpt, setCopyOpt] = useState({});

    useEffect(() => {
        fetch(dashboardUrl, {
            method: "GET",
        })
            .then((res) => res.json())
            .then((res) => {
                if (res?.code === 200) {
                    setData([
                        {
                            ...res.data.projectsData,
                            'name': "projets",
                            'action': "scannés",
                            'icon': "github",
                            'iconColor': "dark",
                            'chartColor': "rgba(33, 37, 41, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.rapportsData,
                            'name': "rapports",
                            'action': "effectués",
                            'icon': "clipboard-data",
                            'iconColor': "danger",
                            'chartColor': "rgba(220, 53, 69, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.jobsData,
                            'name': "jobs",
                            'action': "complétés",
                            'icon': "speedometer",
                            'iconColor': "success",
                            'chartColor': "rgba(25, 135, 84, 0.8)",
                            'chart': true,
                        },
                        {
                            ...res.data.usersData,
                            'name': "utilisateurs",
                            'action': "inscrits",
                            'icon': "people-fill",
                            'iconColor': "primary",
                            'chart': false,
                        },
                    ]);
                }
            });
    }, []);

    useEffect(() => {
        setTimeout(() => {
            document.querySelectorAll('.card.stats').forEach(el => {
                el.classList.add('pop');
            });

            document.querySelectorAll('.card.charts, .card.podium').forEach(el => {
                el.classList.add('show');
            });

        }, 100);

        ChartJS.register(
            CategoryScale,
            LinearScale,
            BarElement,
            Title,
            Tooltip,
            Legend
        );


        let tabCharts = {};
        data.filter((el) => el.chart).forEach((el) => {
            tabCharts[el.name] = {
                [now - 1]: {
                    data: {
                        labels: Object.values(el.countSorted[now - 1]),
                        datasets: [
                            {
                                label: el.name.charAt(0).toUpperCase() + el.name.slice(1),
                                data: Object.values(el.countSorted[now - 1]),
                                borderWidth: 1,
                                backgroundColor: el.chartColor,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 2,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        ticks: {
                            precision: 0
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: `Données de l'année ` + now,
                            },
                        }
                    }
                },
                [now]: {
                    data: {
                        labels: Object.values(el.countSorted[now]),
                        datasets: [
                            {
                                label: el.name.charAt(0).toUpperCase() + el.name.slice(1),
                                data: Object.values(el.countSorted[now]),
                                borderWidth: 1,
                                backgroundColor: el.chartColor,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 2,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        ticks: {
                            precision: 0
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: `Données de l'année ` + now,
                            },
                        }
                    }
                }
            }
        })

        setCopyOpt(tabCharts);
        if (data.length) {
            let colors = data.filter((el) => el.chart).map(el => el.iconColor);
            let tabs = Object.keys(tabCharts).map((el, index) => {
                return {
                    name: el,
                    color: colors[index],
                }
            });
            setChartTabs(tabs);
            setChartTab(tabs[0].name);

            setChartsOpt(tabCharts[tabs[0].name][chartDate]);
        }

        setTimeout(() => {
            document.querySelectorAll('.card.podium .bar').forEach(el => {
                el.classList.add('show');
                el.classList.add('grow');
            });
        }, 500);
    }, [data]);

    useEffect(() => {
        if (data.length) {
            setChartsOpt(copyOpt[chartTab][chartDate]);
        }
    }, [chartDate, chartTab]);

    const changeDate = (target) => {

        document.querySelector('.select-date.selected')?.classList.remove('selected');
        target.classList.add('selected');

        setChartDate(target.dataset.value);
    }

    const changeTab = (target) => {

        let selected = document.querySelector('.chart-tab.selected')

        // selected?.classList.remove(selected.dataset.hover);
        selected?.classList.remove('selected');

        // target.classList.add(target.dataset.hover);
        target.classList.add('selected');

        setChartTab(target.dataset.tab);
    }

    return (
        <div id="dashboard">
            <h1 className="dashboard-title">Dashboard</h1>
            <div className="row">
                {
                    data.map((typeData) => (
                        <Fragment key={typeData.name}>
                            <div className="col-xl-3 col-md-5 col-xs-12">
                                <div className="card stats m-2">
                                    <div className="card-body">
                                        <div className="row">
                                            <div className="col">
                                                <h5 className="card-title text-uppercase text-muted mb-0">{typeData.name.charAt(0).toUpperCase() + typeData.name.slice(1)}</h5>
                                                <span className="h2 font-weight-bold mb-0">{typeData.nbObjects}</span>
                                            </div>
                                            <div className="col-4">
                                                <div className={"icon icon-shape bg-" + typeData.iconColor + " text-white rounded-circle shadow"}>
                                                    <i className={"bi bi-" + typeData.icon}></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Fragment>
                    ))
                }
            </div>
            <div className="row">
                <div className="card charts fade">
                    <div className="card-body">
                        <div className="radios">
                            <div
                                onClick={(e) => changeDate(e.target)}
                                className="select-date"
                                data-value={now - 1}
                                id={"chart-date-" + (now - 1)}
                            >
                                {now - 1}
                            </div>
                            <div
                                onClick={(e) => changeDate(e.target)}
                                className="select-date selected"
                                data-value={now}
                                id={"chart-date-" + (now)}
                            >
                                {now}
                            </div>
                        </div>

                        <div className="chart-tabs row">
                            {
                                chartTabs.map((tab) => (
                                    <button key={tab.name} className={"col-4 chart-tab bg-" + tab.color} onClick={(e) => changeTab(e.target)} data-tab={tab.name} data-hover={"bg-" + tab.color}>
                                        {tab.name.charAt(0).toUpperCase() + tab.name.slice(1)}
                                    </button>
                                ))
                            }
                        </div>

                        {
                            chartsOpt?.options && chartsOpt?.data ? (
                                <Bar
                                    options={chartsOpt.options}
                                    data={chartsOpt.data}
                                />
                            ) : ""
                        }
                    </div>
                </div>
            </div>
            <div className="row">
                {
                    data.filter((object) => !object.chart).map((typeData) => (
                        <Fragment key={typeData.name}>
                            <div className="card podium fade">
                                <div className="card-body text-center">
                                    <div className="card-title">
                                        <h4>Classment d'utilisation (rapports effectués)</h4>
                                    </div>
                                    <div className="scoreboard row">
                                        {
                                            typeData.podium.map((user) => (
                                                <Fragment key={user.email}>
                                                    <div className="col-3 bar-div">
                                                        <p className="rapport-count">{user.countRapport}</p>
                                                        <div className={"bar fade " + user.place}>
                                                            {user.placeNumber}
                                                        </div>
                                                        <p>{user.name} <br /> <small>{user.email}</small></p>
                                                    </div>
                                                </Fragment>
                                            ))
                                        }
                                    </div>
                                    <div className="text-center">
                                        <a className="btn btn-primary" href={usersUrl}>Accéder aux utilisateurs <i className="bi bi-people-fill"></i></a>
                                    </div>
                                </div>
                            </div>
                        </Fragment>
                    ))
                }

            </div>
        </div >
    );
}
