
class stpronk {

  constructor () {

    /**
     * Navigation variables
     */
    this.navigation = {
      'active' : false,
      'activeClass' : 'navigation--active',
      'selector' : '.navigation'
    };

    /**
     * Target Reader variables
     */
    this.reader = {
      'selector' : '[data-function=target-reader]',
      'target' : null,
      'element' : null,
      'double' : null,
    };

    /**
     * Contact Variables
     */
    this.contact = {
      'active' : false,
      'activeClass' : 'contact__information--active',
      'selector' : '[data-function=contact]',
      'target' : 'contact__information',
      'output' : 'contact-information__output',
      'value' : '',
      'copy' : {
        'active' : false,
        'selector' : '[data-function=contact-copy]',
        'target' : 'contact__copy-input',
        'feedback' : {
          'class' : 'contact__copy-feedback',
          'value' : 'Copied!'
        }
      }
    };

    /**
     * Always safe the initial states off the variables
     */
    this.initial = {
      'navigation' : this.navigation,
      'reader' : this.reader,
      'contact' : this.contact
    };

    this.eventListeners();
  }

  eventListeners() {
    document.querySelectorAll(this.reader.selector).forEach((element) => {
      element.addEventListener('click', (event) => {
        this.targetReader(event);
      });
    });
    document.querySelectorAll(this.contact.selector).forEach((element) => {
      element.addEventListener('click', (event) => {
        this.contactToggle(event);
      });
    });
    document.querySelector(this.contact.copy.selector).addEventListener('click', (event) => {
      this.contactCopy(event);
    });

    if( this.navigation.selector ) {
      document.querySelector(this.navigation.selector).addEventListener('mouseenter', (event) => {
        this.navigationToggle(event, true);
      }, false);
      document.querySelector(this.navigation.selector).addEventListener('mouseleave', (event) => {
        this.navigationToggle(event)
      }, false);
    }
  }

  targetReader (event) {
    event.preventDefault();

    if ( event.target.getAttribute('data-navigation') ) {
      if ( ! this.navigation.active ) {
        return;
      }
    }

    this.reader.target = event.target.getAttribute('data-target');
    const double = event.target.getAttribute('data-double-click');

    if ( double && double !== this.reader.double ) {
      return this.reader.double = double;
    }

    if ( this.reader.target.startsWith('http') ) {
      window.open(this.reader.target, '_blank');
      return this.reader = this.initial.reader;
    }

    this.reader.element = document.querySelector('.' + this.reader.target);

    if ( this.reader.element ) {
      this.reader.element.scrollIntoView(true);
      return this.reader = this.initial.reader;
    }
  }

  navigationToggle (event, hover = false) {
    if ( ! hover ) {
      event.target.classList.remove(this.navigation.activeClass);
      return this.navigation.active = false;
    }

    event.target.classList.add(this.navigation.activeClass);
    return setTimeout(() => {
      this.navigation.active = true
    }, 200)
  }

  contactToggle(event) {
    this.contact.value = event.target.getAttribute('data-content');

    const target = document.querySelector('.' + this.contact.target);
    const output = document.querySelector('.' + this.contact.output);

    output.innerHTML = this.contact.value;

    if ( ! this.contact.active ) {
      target.classList.add(this.contact.activeClass);
      this.contact.active = true;
    }
  }

  contactCopy(event) {
    // This shouldn't happen if the contact is not active or when the contact copy is active
    if ( ! this.contact.active || this.contact.copy.active ) {
      return;
    }

    this.contact.copy.active = true;

    const input = document.createElement('input');
    input.classList.add(this.contact.copy.target);
    input.setAttribute('value', this.contact.value);
    document.querySelector('.' + this.contact.target).appendChild(input);

    input.focus();
    input.select();

    try {
      document.execCommand('copy');

      event.target.classList.add('fa-check');
      event.target.classList.remove('fa-files-o');

      setTimeout(() => {
        event.target.classList.add('fa-files-o');
        event.target.classList.remove('fa-check');
        this.contact.copy.active = false;
      }, 700)
    } catch (err) {
      alert('Oops, unable to copy');
    }
  }
}

new stpronk();