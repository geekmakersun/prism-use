# 代码高亮功能使用文档

## 功能说明
本文档介绍如何为HTML页面添加代码高亮功能，支持：
- **语法高亮**：支持多种编程语言，包括PHP、JavaScript、Bash等
- **自动行号显示**：自动为代码块添加行号，无需手动配置
- **代码语言显示**：自动显示代码块使用的编程语言
- **一键复制功能**：自动为代码块添加复制按钮，方便用户复制代码
- **响应式设计**：适配不同屏幕尺寸
- **支持多种主题**：可根据需要更换Prism.js主题

## 技术栈
- Prism.js 1.30.0：用于代码高亮
- 纯JavaScript：用于添加复制功能和语言标签

## 资源文件说明
所有资源已下载到本地，位于以下目录：
- CSS文件：`css/`
  - `prism.css`：Prism主题样式
  - `prism-line-numbers.css`：行号样式
- JS文件：`js/`
  - `prism-core.js`：Prism核心功能
  - `prism-line-numbers.js`：行号插件
  - `prism-autoloader.js`：自动加载语言支持
  - `components/`：各种语言的支持文件

## HTML页面使用方法

### 步骤1：在HTML中引入资源
在HTML文件的`<head>`标签中添加：
```html
<!-- 引入Prism.js CSS -->
<link rel="stylesheet" href="css/prism.css">
<!-- 引入line-numbers插件的CSS样式 -->
<link rel="stylesheet" href="css/prism-line-numbers.css">
```

在HTML文件的`<body>`标签末尾，`</body>`标签前添加：
```html
<!-- 引入Prism.js JS -->
<script src="js/prism-core.js"></script>
<script src="js/prism-line-numbers.js"></script>
<script src="js/prism-autoloader.js"></script>

<!-- 代码高亮初始化和功能配置 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 配置autoloader的语言路径
    if (Prism.plugins && Prism.plugins.autoloader) {
        Prism.plugins.autoloader.languages_path = 'js/components/';
    }
    
    // 为所有代码块添加行号类
    const preElements = document.querySelectorAll('pre[class*="language-"]');
    preElements.forEach(pre => {
        // 确保行号类存在
        if (!pre.classList.contains('line-numbers')) {
            pre.classList.add('line-numbers');
        }
        
        // 确保有相对定位
        pre.style.position = 'relative';
        
        // 移除旧的元素
        const oldButtons = pre.querySelectorAll('.copy-btn');
        oldButtons.forEach(btn => btn.remove());
        
        // 移除旧的语言标签
        const oldLangLabels = pre.querySelectorAll('.language-label');
        oldLangLabels.forEach(label => label.remove());
        
        // 获取语言名称
        let language = 'text';
        const codeElement = pre.querySelector('code');
        if (codeElement) {
            const codeClasses = codeElement.className;
            const langMatch = codeClasses.match(/language-([a-zA-Z0-9-_]+)/);
            if (langMatch && langMatch[1]) {
                language = langMatch[1];
            }
        }
        
        // 创建语言标签
        const langLabel = document.createElement('div');
        langLabel.className = 'language-label';
        langLabel.innerText = language;
        langLabel.style.cssText = `
            position: absolute;
            top: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: bold;
            z-index: 10;
            text-transform: uppercase;
            letter-spacing: 1px;
        `;
        
        // 创建复制按钮
        const copyBtn = document.createElement('button');
        copyBtn.className = 'copy-btn';
        copyBtn.innerText = '复制';
        copyBtn.style.cssText = `
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 12px;
            cursor: pointer;
            z-index: 10;
            transition: background-color 0.2s;
        `;
        
        // 添加复制功能
        copyBtn.addEventListener('click', () => {
            const code = pre.querySelector('code')?.textContent || pre.textContent;
            navigator.clipboard.writeText(code).then(() => {
                copyBtn.innerText = '已复制';
                copyBtn.style.backgroundColor = '#4CAF50';
                setTimeout(() => {
                    copyBtn.innerText = '复制';
                    copyBtn.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                }, 2000);
            }).catch(() => {
                copyBtn.innerText = '失败';
                copyBtn.style.backgroundColor = '#f44336';
                setTimeout(() => {
                    copyBtn.innerText = '复制';
                    copyBtn.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                }, 2000);
            });
        });
        
        // 添加元素到 pre 标签
        pre.appendChild(langLabel);
        pre.appendChild(copyBtn);
    });
    
    // 初始化Prism高亮，确保行号显示
    if (typeof Prism !== 'undefined') {
        // 先移除旧的行号
        document.querySelectorAll('.line-numbers-rows').forEach(row => row.remove());
        
        // 重新高亮所有代码块，确保行号生成
        Prism.highlightAll();
    }
});
</script>
```

### 步骤2：在HTML中使用代码块
在HTML文件中，使用以下格式添加代码块：
```html
<pre class="language-语言"><code class="language-语言">// 代码内容
</code></pre>
```
例如：
```html
<pre class="language-javascript"><code class="language-javascript">function hello() {
    console.log("Hello, World!");
}
</code></pre>
```

## 主题样式
Prism.js支持多种主题，你可以在下载时选择不同的主题，或者手动替换`prism.css`文件。

## 注意事项
1. 确保Prism.js资源路径正确
2. 避免与页面现有样式冲突
3. 可以根据需要调整复制按钮样式
4. 可以根据需要调整语言标签样式，包括位置、颜色、字体等
5. 支持的语言取决于`js/components/`目录下的语言组件文件
6. 行号、语言标签和复制功能是自动添加的，无需手动配置
7. 复制功能依赖浏览器的Clipboard API，确保浏览器支持
8. 语言标签显示的是代码块的实际语言类名，可以通过修改JavaScript代码调整显示格式

## 自定义样式
如果你想自定义样式，可以在HTML文件的`<style>`标签或外部CSS文件中添加以下样式：

```css
/* 代码块容器样式 */
pre[class*="language-"] {
    /* 自定义代码块样式 */
}

/* 行号样式 */
pre.line-numbers {
    padding-left: 3.8em !important;
}

/* 行号容器样式 */
.line-numbers-rows {
    /* 自定义行号容器样式 */
}

/* 语言标签样式 */
.language-label {
    /* 自定义语言标签样式 */
}

/* 复制按钮样式 */
button.copy-btn {
    /* 自定义复制按钮样式 */
}

/* 复制按钮悬停样式 */
button.copy-btn:hover {
    /* 自定义复制按钮悬停样式 */
}

/* 复制成功状态样式 */
button.copy-btn.copied {
    /* 自定义复制成功样式 */
}
```

## 支持的语言
Prism.js支持多种编程语言，包括但不限于：
- **前端开发**：JavaScript、HTML、CSS、TypeScript、React (JSX/TSX)
- **后端开发**：PHP、Python、Java、C++、C#、Ruby、Go
- **脚本语言**：Bash、Shell、PowerShell、Batch
- **数据格式**：JSON、XML、YAML、INI、SQL
- **文档格式**：Markdown、LaTeX
- **配置文件**：Dockerfile、Nginx配置、Apache配置

你可以在`js/components/`目录下查看当前支持的语言组件，也可以从Prism.js官网下载更多语言组件添加到该目录。
