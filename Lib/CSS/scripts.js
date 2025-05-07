

const setFavicon = () => {
    const favicon = document.querySelector('link[rel="icon"]');
    favicon.href = (window.matchMedia('(prefers-color-scheme: dark)').matches)
                    ? 'Lib\Images\Logo-blue.png'
                    : 'Lib\Images\Logo.png';
};

setFavicon();

window
    .matchMedia('(prefers-color-scheme: dark)')
    .addEventListener('change', setFavicon);
