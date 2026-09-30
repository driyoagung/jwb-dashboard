// Build sederhana tanpa dependensi.
//   node build.mjs                      -> hasilkan dist/*.html (siap dibuka langsung)
//   node build.mjs --preview out.html   -> hasilkan 1 file pratinjau (SPA) berisi semua halaman
//
// Sintaks template:
//   {{> nama-partial prop="nilai"}}     sisipkan src/partials/nama-partial.html
//   {{prop}} / {{prop||default}}        nilai prop di dalam partial
//   {{#if prop}}...{{/if}}              tampil bila prop diisi
//   {{#each users}}...{{this.name}}{{/each}}   ulang data dari src/data.mjs
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import data from './src/data.mjs';
import icons from './src/icons.mjs';

const root = path.dirname(fileURLToPath(import.meta.url));
const src = (...p) => path.join(root, 'src', ...p);
const dist = path.join(root, 'dist');
const args = process.argv.slice(2);
const previewOut = args.includes('--preview') ? args[args.indexOf('--preview') + 1] : null;

const read = (f) => fs.readFileSync(f, 'utf8');
const get = (obj, p) => p.split('.').reduce((o, k) => (o == null ? o : o[k]), obj);

function sprite() {
  const symbols = Object.entries(icons)
    .map(([name, body]) => `<symbol id="i-${name}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${body}</symbol>`)
    .join('\n  ');
  return `<svg xmlns="http://www.w3.org/2000/svg" width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">\n  ${symbols}\n</svg>`;
}

const cache = {};
const partial = (name) => (name === 'sprite' ? sprite() : (cache[name] ??= read(src('partials', name + '.html'))));

function render(str) {
  str = str.replace(/\{\{#each ([\w.]+)\}\}([\s\S]*?)\{\{\/each\}\}/g, (_, p, body) => {
    const list = get(data, p);
    if (!Array.isArray(list)) throw new Error('each: data tidak ditemukan: ' + p);
    return list
      .map((item, i) =>
        body
          .replace(/\{\{this\.([\w.]+)\}\}/g, (_, k) => String(get(item, k) ?? ''))
          .replace(/\{\{@index\}\}/g, i)
          .replace(/\{\{@n\}\}/g, i + 1)
      )
      .join('');
  });
  return str.replace(/\{\{>\s*([\w-]+)((?:\s+[\w-]+="[^"]*")*)\s*\}\}/g, (_, name, attrs) => {
    const props = {};
    attrs.replace(/([\w-]+)="([^"]*)"/g, (m, k, v) => (props[k] = v));
    let tpl = partial(name);
    tpl = tpl.replace(/\{\{#if ([\w-]+)\}\}([\s\S]*?)\{\{\/if\}\}/g, (_, k, b) => (props[k] ? b : ''));
    tpl = tpl.replace(/\{\{([\w-]+)(?:\|\|([^}]*))?\}\}/g, (m, k, d) => (k in props ? props[k] : d !== undefined ? d : m));
    return render(tpl);
  });
}

function loadPage(file) {
  let s = read(src('pages', file));
  const meta = {};
  const m = s.match(/^<!--page([^>]*)-->\s*/);
  if (m) {
    m[1].replace(/([\w-]+)="([^"]*)"/g, (_, k, v) => (meta[k] = v));
    s = s.slice(m[0].length);
  }
  return { key: file.replace('.html', ''), meta: { layout: 'app', group: '', ...meta }, content: render(s) };
}

function assemble(layoutName, vars, content, { inline = false, templates = '' } = {}) {
  let out = render(read(src('layouts', layoutName + '.html')));
  out = out.replace('{{content}}', () => content).replace('<!--spa-templates-->', () => templates);
  out = out.replace(/\{\{(title|group)\}\}/g, (_, k) => vars[k] ?? '');
  if (inline) {
    out = out.replace(/<script src="assets\/([\w.-]+)"><\/script>/g, (_, f) => `<script>${read(src('assets', f))}</script>`);
  }
  return out;
}

const files = fs.readdirSync(src('pages')).filter((f) => f.endsWith('.html')).sort();
const pages = files.map(loadPage);

if (previewOut) {
  const templates =
    '<script>window.__SPA__ = true;</script>\n' +
    pages
      .map((p) => `<template data-page="${p.key}" data-title="${p.meta.title}" data-group="${p.meta.group}" data-layout="${p.meta.layout}">\n${p.content}\n</template>`)
      .join('\n');
  const html = assemble('app', { title: 'Dashboard', group: 'Platform' }, '', { inline: true, templates });
  fs.writeFileSync(previewOut, html);
  console.log('Pratinjau:', previewOut, (html.length / 1024).toFixed(0) + ' KB,', pages.length, 'halaman');
} else {
  fs.rmSync(dist, { recursive: true, force: true });
  fs.mkdirSync(path.join(dist, 'assets'), { recursive: true });
  for (const f of fs.readdirSync(src('assets'))) fs.copyFileSync(src('assets', f), path.join(dist, 'assets', f));
  for (const p of pages) {
    fs.writeFileSync(path.join(dist, p.key + '.html'), assemble(p.meta.layout, p.meta, p.content));
  }
  console.log('Selesai:', pages.length, 'halaman ->', dist);
}
