const state = {
    content: ''
}

const mutations = {
    setContent (state, { content, timeout }) {
        state.comtent = content

        if (typeof timeout === 'undefined') {
            timeout = 3000
        }

        setTimeout(() => (state.content = ''), timeout)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}