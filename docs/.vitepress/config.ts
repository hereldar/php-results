
// https://vitepress.dev/reference/site-config
export default {
  lang: 'en-US',
  title: "Hereldar\\Results",
  description: "An opinionated result type to manage the results in any way you choose",
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    nav: nav(),
    sidebar: {
      '/': sidebarGuide(),
      '/reference/': sidebarReference()
    },
    outline: [2, 3],
    search: {
      provider: 'local'
    },
    socialLinks: [
      { icon: 'github', link: 'https://github.com/hereldar/php-results' }
    ]
  },
  base: '/php-results/'
}

function nav() {
  return [
    {
      text: 'Guide',
      items: [
        {text: 'Getting Started', link: '/'}
      ]
    },
    {
      text: 'Reference',
      activeMatch: '/reference/',
      items: [
        { text: 'Ok', link: '/reference/ok' },
        { text: 'Error', link: '/reference/error' },
        { text: 'Result', link: '/reference/result' }
      ]
    }
  ]
}

function sidebarGuide() {
  return [
    {text: 'Getting Started', link: '/'}
  ]
}

function sidebarReference() {
  return [
    {
      text: 'Reference',
      link: '/reference/',
      items: [
        { text: 'Ok', link: '/reference/ok' },
        { text: 'Error', link: '/reference/error' },
        { text: 'Result', link: '/reference/result' }
      ]
    }
  ]
}
