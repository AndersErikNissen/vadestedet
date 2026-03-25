"use strict";
// @@ UTILILTY
const OBSERVE = (callback, target, options = {}) => {
  const OBSERVE_CALLBACK = (entries) => {
    entries.forEach((entry) => {
      callback.apply(entry);
    });
  };

  const OBSERVER = new IntersectionObserver(OBSERVE_CALLBACK, options);

  OBSERVER.observe(target);
}

// @@ TOGGLE THE HEADER
(function(d) {
  const THE_HEADER = d.querySelector(".the-header");
  const OBSERVER_ZONE = d.querySelector(".toggle-the-header-see-through");
  const CLASS = "see-through";
  const TRANSITION_CLASS = "transition-see-through";

  if (!THE_HEADER || !OBSERVER_ZONE) return;

  function callback() {
    if (!THE_HEADER.classList.contains(TRANSITION_CLASS)) {
      THE_HEADER.classList.add(TRANSITION_CLASS)
    }

    if (this.isIntersecting) {
      if (!THE_HEADER.classList.contains(CLASS)) {
        THE_HEADER.classList.add(CLASS);
      } 
    } else {
      if (THE_HEADER.classList.contains(CLASS)) {
        THE_HEADER.classList.remove(CLASS);        
      } 
    }
  }

  OBSERVE(callback, OBSERVER_ZONE);
})(document);

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

      this.modal.addEventListener("click", (e) => {
        if (e.target === this.modal) {
          this.close();
        }
      });
  
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


// @@ CAROUSEL
class Carousel {
  constructor({carousel, delay = 4000}) {
    this.carousel = carousel;
    this.swap = {
      timeout: null,
      isAnimating: false,
    }

    this.loop = {
      start: null,
      timeout: null,
      delay: delay,
      remaining: delay,
    };

    if (this.carousel) {
      this.items = Array.from(this.carousel.querySelectorAll('.carousel-item'));

      if (this.items.length < 2) {
        this.carousel.classList.add("inactive-carousel");
        return;
      }

      let foundIndex = this.items.findIndex((item) => item.classList.contains('active'));
      this.index = foundIndex === -1 ? 0 : foundIndex;

      this.loopStart();
    }
  }

  get index() {
    return this._index;
  }

  set index(i) {
    let index = i;

    if (i > this.items.length - 1) {
      index = 0;
    } else if (i < 0) {
      index = this.items.length - 1;
    }

    this._index = index;
  }

  removeClass(element, cls) {
    if (element.classList.contains(cls)) {
      element.classList.remove(cls);
    }
  }

  swapTimeout() {
    this.swap.timeout = setTimeout(() => {
      this.removeClass(this.items[this.previousIndex], 'display');
      this.removeClass(this.items[this.previousIndex], 'inactive');

      this.swap.timeout = false;
    }, 500);
  }

  swapAnimation() {
    if (this.swap && this.swap.timeout) return;

    if (!this.swap.isAnimating) {
      this.swap.isAnimating = true;
      this.carousel.classList.add('is-animating');
    }

    const prev = this.items[this.previousIndex];
    const current = this.items[this.index];

    prev.classList.add("display");
    current.classList.add("display");

    this.items[this.index].classList.add("display");
    
    window.requestAnimationFrame(() => {
      window.requestAnimationFrame(() => {
        prev.classList.remove("active");
        prev.classList.add("inactive");

        current.classList.add("active");
        this.swapTimeout();
      })
    });
  }

  swapItem(i) {
    clearTimeout(this.loop.timeout);

    this.loop.remaining = this.loop.delay;

    this.previousIndex = this.index;
    this.index = i;

    this.swapAnimation();

    if (!this.loop.isPaused) {
      this.loopStart();
    }
  }

  swapToNextItem() {
    this.swapItem(this.index + 1);
  }

  swapToPreviousItem() {
    this.swapItem(this.index - 1);
  }

  loopStart() {
    this.loop.isPaused = false;

    this.loop.start = Date.now();

    const DELAY = this.loop.remaining > 0 ? this.loop.remaining : 0;

    this.loop.timeout = setTimeout(() => {
      this.swapToNextItem();
    }, DELAY);
  }

  loopPause() {
    if (this.loop.isPaused || !this.loop.start) return;

    this.loop.isPaused = true;
    clearTimeout(this.loop.timeout);
    this.loop.remaining = Math.max(0, this.loop.remaining - (Date.now() - this.loop.start));
  }

  loopResume() {
    this.loopStart();
  }
};

/**
 * IDEAS FOR BETTER AND MORE CLEAR CODE
 * 1. Is .display needed?
 * 2. Consider using [data-*] more, for things like the state of the carousel
 * 3. Would it be more clean to have Class for the Carousel-Item itself (with maybe methods like: set active, handleClasses)
 * 4. Rename the methods of the carousel to things like: next(), prev()...
 * 5. Rethink the loop / pause mechanic
 * 6. Read article: https://www.wiktorwisniewski.dev/blog/build-simple-javascript-slider
 * 7. Use RAF() instead of setTimeout() + Date.now() ?
 */

const CAROUSELS = document.querySelectorAll(".carousel").forEach((element) => {
  return new Carousel({
    carousel: element
  });
});


// @@ ACCORDION
class Accordion {
  constructor({containerReference}) {
    this.container = containerReference;

    const itemElements = Array.from(this.container.querySelectorAll(".accordion__item"));
    
    this.items = itemElements.map((element) => new AccordionItem({
      containerReference: element,
      parentReference: this,
    }));
  }

  closeItemDrawers(excludedItem) {
    this.items.forEach((item) => {
      if (excludedItem && excludedItem !== item) {
        item.closeDrawer();
      };
    });
  }
}

class AccordionItem {
  constructor({containerReference, parentReference}) {
    this.container = containerReference;
    
    if (!this.container) return;
    
    this.parent = parentReference;

    this.header = this.container.querySelector(".accordion__header");

    if (this.header) {
      this.header.addEventListener("click", () => this.toggleDrawer());
    };
  }

  get isOpen() {
    return this.container.getAttribute("data-is-open") === "true";
  }

  set isOpen(bool) {
    this.container.setAttribute("data-is-open", bool.toString());
  }

  closeDrawer() {
    this.isOpen = false;
  }

  openDrawer() {
    this.isOpen = true;
    this.parent.closeItemDrawers(this);
  }

  toggleDrawer() {
    if (this.isOpen) {
      this.closeDrawer();
    } else {
      this.openDrawer();
    }
  }
}

const ACCORDION_ELEMENTS = Array.from(document.querySelectorAll(".accordion"));
const ACCORDIONS = ACCORDION_ELEMENTS.map((element) => new Accordion({
  containerReference: element,
}));