const getInstallablePlugins = function (mandatory = {}, recommended = {}, defaultOff = []) {
  let plugins = [...Object.keys(recommended), ...Object.keys(mandatory)]
  plugins = plugins.reduce((o, key) => Object.assign(o, { [key]: true }), {})

  defaultOff.forEach( function (offPlugin) {
    plugins[offPlugin] = false
  } )

  return plugins
}

export {
  getInstallablePlugins
}
