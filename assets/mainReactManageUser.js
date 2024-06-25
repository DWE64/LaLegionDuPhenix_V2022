import './bootstrap.js';
import React from 'react';
import { createRoot } from 'react-dom/client';
import AppReact from "./AppReact";

// console.log(createRoot);

const element = document.getElementById('root-user-manage');
console.log(element);
console.log(element.data('role'));
// Render your React component instead
const root = createRoot(element);
root.render(
    <React.StrictMode>
        {/*<AppReact state={element.data}/>*/}
    </React.StrictMode>
);