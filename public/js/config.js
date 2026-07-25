const runtimeAppUrl = typeof globalThis.APP_URL === 'string' && globalThis.APP_URL.trim() !== ''
  ? globalThis.APP_URL.trim()
  : 'http://localhost/projumi';

export const API_CONFIG = runtimeAppUrl.replace(/\/$/, '');
