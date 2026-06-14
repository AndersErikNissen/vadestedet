"use strict";

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

class Header {
  _colorSwaps = {
    'white-brown': 'blue-brown',
    'white-green': 'yellow-brown',
    'yellow-brown': 'white-green',
    'yellow-green': 'blue-brown',
    'blue-brown': 'white-brown',
    'brown-white': 'green-yellow',
    'brown-yellow': 'green-white',
    'brown-blue': 'green-white',
    'green-white': 'brown-yellow',
    'green-yellow': 'brown-white',
  };

  constructor({header}) {
    this.header = header;

    if (!this.header.element) return; 
     
    this.triggers = document.querySelectorAll(".color-theme-swap-trigger");
    
    if (this.triggers.length === 0) return;

    this.window = window;
    this.observer = this.options;

    this.startObserving();

    window.addEventListener("resize", () => {
      if (window.innerWidth !== this.window.width) {
        this.triggers.forEach((trigger) => {
          this.observer.unobserve(trigger);
        });
        
        this.observer.disconnect();

        this.window = window;
        this.observer = this.options;

        this.startObserving();
      }
    });
  }

  get header() {
    return this._header || {
      element: null,
      height: 0,
    };
  }

  set header(element) {
    this._header = {
      element: element,
      height: element.getBoundingClientRect().height,
    };
  }

  get window() {
    return this._window || {
      height: 0,
    }
  }

  set window(currentWindow) {
    this._window = {
      height: currentWindow.innerHeight,
      width: currentWindow.innerWidth,
    }
  }

  get options() {
    const TOP_OFFSET = this.header.height / 2;
    const BOTTOM_OFFSET = this.window.height - TOP_OFFSET + 5;

    return {
      root: null,
      threshold: 0,
      rootMargin: `-${TOP_OFFSET}px 0px -${BOTTOM_OFFSET}px 0px`,
    };
  }

  get observer() {
    return this._observer;
  }

  set observer(options) {
    this._observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        const COLOR = this._colorSwaps[entry.target.dataset.colorTheme || 'white-brown'];

        if (entry.isIntersecting) {
          this.header.element.setAttribute("data-color-theme", COLOR);
        }
      });
    }, options);
  }

  observeCallback(trigger) {

  }

  startObserving() {
    this.triggers.forEach((trigger) => {
      this.observer.observe(trigger);
    });
  }
}

const THE_HEADER = new Header({
  header: document.querySelector(".the-header"),
})


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

      this.btns.forEach((btn) => btn.setAttribute("aria-expanded", true));

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

    this.btns.forEach((btn) => btn.setAttribute("aria-expanded", false));
    
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

class Banner {
  constructor({banner}) {
    if (!banner) return;

    this.banner = banner;
    this.track = this.banner.querySelector(".banner-track");
    this.blueprint = this.banner.querySelector(".banner-blueprint");
    
    if (!this.track || !this.blueprint) return;
    this.lastWindowWidth = window.innerWidth;

    document.fonts.ready.then(this.update.bind(this));

    window.addEventListener('resize', () => {
      let windowWidth = window.innerWidth;

      if (windowWidth !== this.lastWindowWidth) {
        this.lastWindowWidth = windowWidth;
        this.update();
      }
    });
  }

  get lastWindowWidth() {
    return this._lastWindowWidth;
  }

  set lastWindowWidth(width) {
    this._lastWindowWidth = width;
  }

  renderScrollTracks() {
    let pixelsPerSecond = 20;
    let contentWidth = this.blueprint.offsetWidth;
    let copiesNeeded = Math.ceil(this.lastWindowWidth / contentWidth) + 1;
    let duration = contentWidth * copiesNeeded / pixelsPerSecond;

    this.track.innerHTML = "";

    for (let i = 0; i < 2; i++) {
      let scrollTrack = document.createElement("div");
      scrollTrack.className = 'banner-scroll-track';

      let content = "";
      for (let c = 0; c < copiesNeeded; c++) {
        content += this.blueprint.innerHTML;
      }

      scrollTrack.innerHTML = content;
      scrollTrack.style.animationDuration = `${duration}s`;
      this.track.appendChild(scrollTrack);
    }
  }

  update() {
    if (this.banner.classList.contains("active")) {
      this.banner.classList.remove("active");
    }

    this.renderScrollTracks();

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        this.banner.classList.add("active");
      });
    });
  }
}

const BANNER_ELEMENTS = Array.from(document.querySelectorAll('.banner, .small\\:banner'));
const BANNERS = BANNER_ELEMENTS.map((element) => new Banner({
  banner: element,
}));

(function() {
  const HEADER_BANNER = document.querySelector(".the-header-banner");
  const HEADER_BANNER_BTN = document.querySelector(".the-header-banner-btn");

  if (HEADER_BANNER && HEADER_BANNER_BTN) {
    HEADER_BANNER_BTN.addEventListener("click", () => HEADER_BANNER.remove());
  }
})();

const OBSERVE = (callback, target, options = {}) => {
  const OBSERVE_CALLBACK = (entries) => {
    entries.forEach((entry) => {
      callback.apply(entry);
    });
  };

  const OBSERVER = new IntersectionObserver(OBSERVE_CALLBACK, options);

  OBSERVER.observe(target);
}

const CLAMP = (value, min = 0, max = 1) => {
  return Math.min(max, Math.max(min, value));
};

const LERP = (from, to, ease) => {
  return from * (1 - ease) + to * ease;
};

class ScrollProgressSection {
  state = {
    ticking: false,
    looping: false,
  };
  
  progress = {
    cache: 0,
    current: 0,
    scroll: 0,
  }

  constructor({section, observe, startFromDivider}) {
    this.section = section;
    this.observe = observe || section;
    this.startFromDivider = startFromDivider;

    if (this.section) {
      this.bindEvents();
    }
  }

  loop() {
    const RECT = this.observe.getBoundingClientRect();
    const EASE = 0.2;
    const START_PROGRESS_FROM = window.innerHeight * this.startFromDivider;
    const TOTAL_DISTANCE = (window.innerHeight - START_PROGRESS_FROM) + RECT.height;
    const DISTANCE_TRAVELED = window.innerHeight - RECT.bottom + RECT.height;
    
    let targetScroll = DISTANCE_TRAVELED / TOTAL_DISTANCE;

    // Guard Rails: Handle hard document boundaries cleanly
    if (window.scrollY === 0) {
      targetScroll = 0;
    } else if (RECT.bottom < 0) {
      targetScroll = 1;
    }

    this.progress.scroll = CLAMP(targetScroll, 0, 1);
    
    this.progress.current = CLAMP(LERP(this.progress.cache, this.progress.scroll, EASE));

    if (Math.abs(this.progress.current - this.progress.scroll) < 0.0001) {
      this.progress.current = this.progress.scroll;
    }

    if (this.progress.cache === this.progress.current) {
      this.state.looping = false;
      return;
    } 

    if (this.progress.cache !== this.progress.current) {
      this.section.style.setProperty("--progress", this.progress.current.toFixed(5));
      this.progress.cache = this.progress.current;

      window.requestAnimationFrame(() => this.loop());
    } 
  }


  bindEvents() {
    window.addEventListener("scroll", () => {
      if (!this.state.ticking) {
        window.requestAnimationFrame(() => {
          if (!this.state.looping) {
            this.state.looping = true;
            this.loop();
          }

          this.state.ticking = false;
        });

        this.state.ticking = true;
      }
    });
  }
}

const INTRODUCTION = new ScrollProgressSection({
  section: document.querySelector(".section-introduction"),
  observe: document.querySelector(".section-introduction-background-slide"),
  startFromDivider: 1,
});

class Filter {
  _state = {
    filters: [],
    items: [],
  }

  constructor({wrapper, btns, clearBtns}) {
    this.wrapper = wrapper;
    this.items = btns;
    this.clearBtns = clearBtns;

    if ( ! this.wrapper || this.items.length === 0 ) return;

    this.items.forEach((item) => {
      item.btn.addEventListener("click", () => {
        this.filters = item;
      });
    });

    this.clearBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        this.clearFilters();
      });
    });
  }

  get items() {
    return this._state.items;
  }

  set items(btns) {
    let items = [];

    btns.forEach((btn) => {
      const ITEM = this.wrapper.querySelector(`#${btn.dataset.filterFor}`);

      if (!ITEM) return;

      items.push({
        btn: btn,
        element: ITEM,
      });
    });

    this._state.items = items;
  }

  get active() {
    return JSON.parse(this.wrapper.dataset.hasFilters || false);
  }

  set active(bool) {
    return this.wrapper.setAttribute('data-has-filters', JSON.stringify(!!bool));
  }

  get filters() {
    return this._state.filters;
  }

  set filters(item) {
    let index = this.filters.indexOf(item);

    if (index > -1) {
      this.removeItem(item, index);
    } else {
      this.addItem(item, index);
    }

    this.active = this.filters.length > 0;
  }

  clearFilters() {
    while(this.filters.length > 0) {
      this.removeItem(this.filters[0], 0);
    }
    
    this.active = false;
  }

  removeItem(item, index) {
    item.btn.classList.remove("active");
    item.element.classList.remove("active");
    this.filters.splice(index, 1);
  }

  addItem(item) {
    item.btn.classList.add("active");
    item.element.classList.add("active");
    this.filters.push(item);
  }
}

const BOARDGAME_FILTER = new Filter({
  wrapper: document.querySelector(".section-boardgames"),
  btns: document.querySelectorAll(".section-boardgames-filter-btn"),
  clearBtns: document.querySelectorAll(".section-boardgames-clear-filter-btn"),
});