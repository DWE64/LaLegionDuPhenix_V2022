import './bootstrap.js';
import React from 'react';
import { createRoot } from 'react-dom/client';
import { AppReact } from "./AppReact";

const element = document.getElementById('root-user-manage');
if (element) {
    const role = element.getAttribute('data-role');
    const root = createRoot(element);

    root.render(
        <React.StrictMode>
            <AppReact state={{ role }} />
        </React.StrictMode>
    );
}