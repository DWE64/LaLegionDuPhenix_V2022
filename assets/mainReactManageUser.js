import './bootstrap.js';
import React from 'react';
import { createRoot } from 'react-dom/client';
import {AppReact} from "./AppReact";

const element = document.getElementById('root-user-manage');
if (element) {
    const initialState = JSON.parse(element.getAttribute('data-initial-state'));
    const root = createRoot(element);

    root.render(
        <React.StrictMode>
            <AppReact state={initialState} />
        </React.StrictMode>
    );
}
