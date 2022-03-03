export default {
  admin: {
    authorize: {
      clientId: 2,
      clientSecret: 'DxxDL2i5WHW4tRVO8CFX4gCWEjoXLMOUtMODdl4n'
    },
    loginRouteName: 'adminLogin',

    dashboardName: 'adminDashboard',

    dashboardFullPath: '/admin/dashboard',

    appName: {
      fullName: '业务管理系统',
      abbrName: '业务'
    },

    locale: 'zh'
  },

  guardNames: [
    {
      label: 'admin',
      value: 'admin'
    }
  ],

  apiUrl: '',

  //Unit is day
  tokenTTL: 1,

  //Unit is day
  refreshTokenTTL: 7,

  showAuthorGitHubUrl: true,
}
