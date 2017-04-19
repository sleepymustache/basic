class ServiceWorkerInstaller {
  // Send a toast message to the user, there is an update
  _toastUpdate(worker) {
    window.dispatchEvent(
      new CustomEvent('toast', { detail: {
        message: 'New update available. Click to reload page.',
        actionName: 'Update Now',
        action: () => {
          worker.waiting.postMessage({refresh: true});
        }
      }})
    );
  }

  // Wait for the installed state, then notify the user
  _trackInstalling (worker) {
    worker.addEventListener('statechange', () => {
      if (worker.installing.state === 'installed') {
        console.dir(worker);
        this._toastUpdate(worker);
      }
    });
  }

  register() {
    if (navigator.serviceWorker) {
      navigator.serviceWorker.register('/sw.js').then((reg) => {
        // If we update the service worker reload the page
        navigator.serviceWorker.addEventListener('controllerchange', () => {
          location.reload();
        });

        // Already the latest version
        if (!navigator.serviceWorker.controller) {
          return;
        }

        // There is a new service working, let then know
        if (reg.waiting) {
          this._toastUpdate(reg);
          return;
        }

        if (reg.installing) {
          this._trackInstalling(reg);
          return;
        }

        reg.addEventListener('updatefound', () => {
          this._trackInstalling(reg);
        });
      });
    } else {
      console.log('Cannot register ServiceWorker.');
    }
  }
}

export default ServiceWorkerInstaller;
