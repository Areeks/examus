const cheerio = require('cheerio')
const axios = require('axios')

async function lightshotImageExtractor(url) {
  try {
    const { data } = await axios.get(url)
    const imgUrl = parseHTML(data)
    return imgUrl
  } catch (err) {
    console.log(err)
    return null
  }
}

function parseHTML(html) {
  const $ = cheerio.load(html)
  const rows = $('.screenshot-image')

  if (rows.length > 0 && rows[0].attribs && rows[0].attribs.src) {
    return rows[0].attribs.src
  }

  return null
}