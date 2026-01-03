# 代码高亮功能使用文档

## 功能说明
本文档介绍如何为HTML页面和WordPress主题添加代码高亮功能，支持：
- 语法高亮
- 行号显示
- 复制到剪贴板功能
- 支持多种编程语言

## 技术栈
- Prism.js 1.30.0：用于代码高亮
- 纯JavaScript：用于添加复制功能

## 资源文件说明
所有资源已下载到本地，位于以下目录：
- CSS文件：`assets/css/`
  - `prism.css`：Prism主题样式
  - `prism-line-numbers.css`：行号样式
- JS文件：`assets/js/`
  - `prism-core.js`：Prism核心功能
  - `prism-line-numbers.js`：行号插件
  - `prism-autoloader.js`：自动加载语言支持
  - `components/`：各种语言的支持文件

## HTML页面使用方法

### 步骤1：在HTML中引入资源
在HTML文件的`<head>`标签中添加：
```html
<!-- 引入Prism.js CSS -->
<link rel="stylesheet" href="assets/css/prism.css">
<!-- 引入line-numbers插件的CSS样式 -->
<link rel="stylesheet" href="assets/css/prism-line-numbers.css">
```

在HTML文件的`<body>`标签末尾，`</body>`标签前添加：
```html
<!-- 引入Prism.js JS -->
<script src="assets/js/prism-core.js"></script>
<script src="assets/js/prism-line-numbers.js"></script>
<script src="assets/js/prism-autoloader.js"></script>

<!-- 代码高亮初始化和复制功能 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 配置autoloader的语言路径
    if (Prism.plugins && Prism.plugins.autoloader) {
        Prism.plugins.autoloader.languages_path = 'assets/js/components/';
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
        
        // 移除旧的复制按钮
        const oldButtons = pre.querySelectorAll('.copy-btn');
        oldButtons.forEach(btn => btn.remove());
        
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
        
        // 添加复制按钮
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

## WordPress主题使用方法

### 步骤1：将资源添加到主题
1. 将`assets`文件夹复制到WordPress主题目录
2. 确保目录结构为：
   ```
   主题目录/
   └── assets/
       ├── css/
       │   ├── prism.css
       │   └── prism-line-numbers.css
       └── js/
           ├── components/
           ├── prism-core.js
           ├── prism-line-numbers.js
           └── prism-autoloader.js
   ```

### 步骤2：使用 functions.php 自动配置
主题已经包含了一个功能完整的 `functions.php` 文件，它会自动处理以下任务：

1. **自动为代码块添加 class**：当在 WordPress 编辑器中使用代码块时，自动为 `<pre>` 标签添加 `line-numbers` 类，为 `<code>` 标签添加 `language-xxx` 类
2. **加载 Prism.js 资源**：自动在后台编辑器和前台加载必要的 CSS 和 JavaScript 文件
3. **配置语言路径**：自动设置 Prism.js 自动加载器的语言组件路径
4. **添加复制功能**：为所有代码块自动添加复制按钮
5. **初始化代码高亮**：确保所有代码块都能正确高亮显示

### 步骤3：自定义配置（可选）
如果你需要自定义配置，可以修改 `functions.php` 文件中的以下功能：

1. **调整代码块样式**：修改 `style.css` 文件中的相关样式
2. **扩展语言支持**：在 `assets/js/components/` 目录下添加更多语言组件
3. **修改复制按钮样式**：在 `functions.php` 文件中调整复制按钮的 CSS 样式
4. **修改自动加载器配置**：调整 `Prism.plugins.autoloader.languages_path` 的值

### 步骤4：手动配置（备用方法）
如果你更倾向于手动配置，可以按照以下步骤操作：

在主题的`header.php`文件中添加：
```html
<!-- 引入Prism.js CSS -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/prism.css">
<!-- 引入line-numbers插件的CSS样式 -->
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/prism-line-numbers.css">
```

在主题的`footer.php`文件末尾，`</body>`标签前添加：
```html
<!-- 引入Prism.js JS -->
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/prism-core.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/prism-line-numbers.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/prism-autoloader.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/assets/js/prism-copy-to-clipboard.js"></script>

<!-- 代码高亮初始化 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 配置autoloader的语言路径
    if (Prism.plugins && Prism.plugins.autoloader) {
        Prism.plugins.autoloader.languages_path = '<?php echo get_template_directory_uri(); ?>/assets/js/components/';
    }
    
    // 初始化Prism高亮
    if (typeof Prism !== 'undefined') {
        Prism.highlightAll();
    }
});
</script>
```

### 步骤5：在WordPress中使用代码块
在WordPress编辑器中，使用以下格式添加代码块：
```
```语言
// 代码内容
```
```
例如：
```
```javascript
function hello() {
    console.log("Hello, World!");
}
```
```

## 主题样式
Prism.js支持多种主题，你可以在下载时选择不同的主题，或者手动替换`prism.css`文件。

## 注意事项
1. 确保Prism.js资源路径正确
2. 避免与主题现有样式冲突
3. 可以根据需要调整复制按钮样式
4. 支持的语言取决于你在下载时选择的语言

## 示例代码
以下是一个完整的示例，展示如何在主题中使用：

```html
<!DOCTYPE html>
<html lang="<?php language_attributes(); ?>">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title(); ?></title>
    
    <!-- 引入Prism.js CSS -->
    <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/prism.css">
    
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <!-- 页面内容 -->
    
    <?php wp_footer(); ?>
    
    <!-- 引入Prism.js JS -->
    <script src="<?php echo get_template_directory_uri(); ?>/assets/js/prism.js"></script>
    
    <!-- 代码高亮初始化和复制功能 -->
    <script>
        // 初始化代码...（见步骤3）
    </script>
</body>
</html>
```

## 自定义样式
如果你想自定义样式，可以在主题的CSS文件中添加以下样式：

```css
/* 行号样式 */
pre.line-numbers {
    padding-left: 3.8em !important;
}

/* 复制按钮样式 */
button.copy-btn {
    /* 自定义样式 */
}
```

## 支持的语言
Prism.js支持多种编程语言，包括但不限于：
- JavaScript
- PHP
- HTML
- CSS
- Python
- Java
- C++
- Ruby
- SQL
- Markdown
- JSON
- XML
- YAML
- INI

你可以在下载Prism.js时选择需要的语言。