"use strict";


// @@ MODALS
class Modal {
  state = {
    displaying: false,
    timeout: false,
    lastTemplate: false,
  };

  constructor({modal, options}) {
    this.modal = modal;

    if (this.modal) {
      this.content = this.modal.querySelector(".modal-content") || this.modal;
      this.name = this.modal.dataset.modal;
  
      this.options = {  
        timing: {
          open: 700,
          close: 700,
          ...(options?.timing || {}),
        },
        classes: {
          disableScroll: 'disable-scroll',
          display: 'display-modal',
          open: 'open-modal',
          close: 'close-modal',
          ...(options?.classes || {}),
        } 
      };
  
      this.btns = document.querySelectorAll(`[data-modal-toggle="${this.name}"]`) || [];

      this.btns.forEach((btn) => {
        let content;
        let template = document.querySelector(`[data-modal-template="${btn.dataset.teleportTemplate}"]`);

        if (template && template instanceof HTMLTemplateElement) {
          content = template.content.cloneNode(true);
        }

        btn.addEventListener("click", () => {
          if (content && this.state.lastTemplate !== template) {
            this.content.replaceWith(content);
            this.state.lastTemplate = template;
          }

          this.toggle();
        });
      });

      document.body.addEventListener("keyup", (e) => {
        if (e.key === "Escape") {
          if (this.state.displaying) {
            this.close();
          }
        }
      });
    }
  }

  open() {
    if (this.state.timeout) {
      return;
    };

    if (!this.state.displaying) {
      document.body.classList.add(this.options.classes.disableScroll);
      this.modal.classList.add(this.options.classes.display);
      this.state.displaying = true;
    }

    window.requestAnimationFrame(() => {
      if (this.modal.classList.contains(this.options.classes.close)) {
        this.modal.classList.remove(this.options.classes.close);
      }

      this.modal.classList.add(this.options.classes.open);

      this.state.timeout = setTimeout(() => {
        this.state.timeout = false;
      }, this.options.timing.open);
    });
  }

  close() {
    if (this.state.timeout) {
      return;
    };

    if(this.modal.classList.contains(this.options.classes.open)) {
      this.modal.classList.remove(this.options.classes.open);
    }

    this.modal.classList.add(this.options.classes.close);
    
    this.state.timeout = setTimeout(() => {
      document.body.classList.remove(this.options.classes.disableScroll);
      this.modal.classList.remove(this.options.classes.close);
      this.modal.classList.remove(this.options.classes.display);

      this.state.displaying = false;
      this.state.timeout = false;
    }, this.options.timing.close);
  }

  toggle() {
    if (this.state.displaying) {
      this.close();
    } else {
      this.open();
    }
  }
}

const THE_MENU = new Modal({
  modal: document.querySelector('[data-modal="the-menu"]'),
});