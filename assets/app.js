import { registerReactControllerComponents } from "@symfony/ux-react";
import "bootstrap-icons/font/bootstrap-icons.css";

import "react-bootstrap/dist/react-bootstrap.min.js";

import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap/dist/js/bootstrap.min.js";

import "./bootstrap.js";
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import "./styles/app.scss";

console.log("This log comes from assets/app.js - welcome to AssetMapper! 🎉");

registerReactControllerComponents(require.context("./react/controllers", true, /\.(j|t)sx?$/));
