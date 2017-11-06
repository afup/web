/**
 * ObjectStorage
 * @constructor
 */
var ObjectStorage = function (itemName) {
  this.name = itemName;
  this.item = {};
  this.useLocalStorage = this.canUseLocalStorage();
  this.load();
};

ObjectStorage.prototype.canUseLocalStorage = function () {
  try {
    localStorage.setItem('test', 'test');
    localStorage.removeItem('test');
    return true;
  } catch (e) {
    return false;
  }
};

/**
 * Load ObjectStorage
 * Init or Load item from LocalStorage if it's possible
 * @returns {object}
 */
ObjectStorage.prototype.load = function () {
  if (this.useLocalStorage && localStorage.hasOwnProperty(this.name)) {
    if (localStorage.getItem(this.name)) {
      try {
        this.item = JSON.parse(localStorage.getItem(this.name)) || {};
      } catch (e) {
        this.item = {};
      }
    }
  }

  return this.item;
};

/**
 * Save ObjectStorage
 * Save item in LocalStorage if it's possible
 */
ObjectStorage.prototype.save = function () {
  if (this.useLocalStorage) {
    localStorage.setItem(this.name, JSON.stringify(this.item));
  }
};

/**
 * Set SubItem in Item and save it
 * @param subItemName
 * @param subItem
 */
ObjectStorage.prototype.set = function (subItemName, subItem) {
  this.item[subItemName] = subItem;
  this.save();
};

/**
 *
 * @param get subitem if exist or return defaultValue
 * @param defaultValue
 * @returns {mixed}
 */
ObjectStorage.prototype.get = function (subItemName, defaultValue) {
  if (this.item.hasOwnProperty(subItemName)) {
    return this.item[subItemName];
  }

  return defaultValue;
};

/**
 *
 * @param remove subitem in Item & save it
 * @param subItemName
 */
ObjectStorage.prototype.remove = function (subItemName) {
  if (this.item.hasOwnProperty(subItemName)) {
    delete this.item[subItemName];
  }
  this.save();
};
