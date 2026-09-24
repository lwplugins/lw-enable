/**
 * Server-provided boot data (SettingsPage inline script).
 */
const boot = window.lwEnable || {};

export const VERSION = boot.version || '';
export const NAMESPACE = boot.namespace || 'lw-enable/v1';
export const DOCS_URL = boot.docsUrl || 'https://lwplugins.com/docs/lw-enable/';
