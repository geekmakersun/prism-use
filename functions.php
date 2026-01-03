<?php
/**
 * My Custom Theme Functions
 *
 * @package My_Custom_Theme
 */

// 确保 WordPress 环境
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 设置主题支持
 */
function my_custom_theme_setup() {
    /*
     * 让 WordPress 管理标题标签。
     */
    add_theme_support( 'title-tag' );
    
    /*
     * 添加文章缩略图支持
     */
    add_theme_support( 'post-thumbnails' );
    
    /*
     * 注册导航菜单
     */
    register_nav_menus( array(
        'menu-1' => esc_html__( 'Primary', 'my-custom-theme' ),
    ) );
    
    /*
     * 添加 HTML5 支持
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ) );
}
add_action( 'after_setup_theme', 'my_custom_theme_setup' );

/**
 * 设置内容宽度
 */
function my_custom_theme_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'my_custom_theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'my_custom_theme_content_width', 0 );

/**
 * 显示文章发布日期
 */
function my_custom_theme_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
        $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
    }

    $time_string = sprintf( $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() ),
        esc_attr( get_the_modified_date( DATE_W3C ) ),
        esc_html( get_the_modified_date() )
    );

    $posted_on = sprintf(
        /* translators: %s: post date. */
        esc_html_x( 'Posted on %s', 'post date', 'my-custom-theme' ),
        '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
    );

    echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: xss ok.

}

/**
 * 显示文章作者
 */
function my_custom_theme_posted_by() {
    $byline = sprintf(
        /* translators: %s: post author. */
        esc_html_x( 'by %s', 'post author', 'my-custom-theme' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );

    echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: xss ok.

}

/**
 * 显示文章缩略图
 */
function my_custom_theme_post_thumbnail() {
    if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
        return;
    }

    if ( is_singular() ) :
        ?>

        <div class="post-thumbnail">
            <?php the_post_thumbnail(); ?>
        </div><!-- .post-thumbnail -->

        <?php
    else :
        ?>

        <a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
            <?php
            the_post_thumbnail( 'post-thumbnail', array(
                'alt' => the_title_attribute( array(
                    'echo' => false,
                ) ),
            ) );
            ?>
        </a>

        <?php
    endif; // End is_singular().
}

/**
 * 显示文章页脚
 */
function my_custom_theme_entry_footer() {
    // 隐藏分类，如果是页面
    if ( 'post' === get_post_type() ) {
        /* translators: used between list items, there is a space after the comma */
        $categories_list = get_the_category_list( esc_html__( ', ', 'my-custom-theme' ) );
        if ( $categories_list ) {
            /* translators: 1: list of categories. */
            printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'my-custom-theme' ) . '</span>', $categories_list ); // WPCS: xss ok.
        }

        /* translators: used between list items, there is a space after the comma */
        $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'my-custom-theme' ) );
        if ( $tags_list ) {
            /* translators: 1: list of tags. */
            printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'my-custom-theme' ) . '</span>', $tags_list ); // WPCS: xss ok.
        }
    }

    if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
        echo '<span class="comments-link">';
        comments_popup_link( // WPCS: xss ok.
            sprintf(
                wp_kses(
                    /* translators: %s: post title */
                    __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'my-custom-theme' ),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                get_the_title()
            )
        );
        echo '</span>';
    }

    edit_post_link(
        sprintf(
            wp_kses(
                /* translators: %s: Name of current post. Only visible to screen readers */
                __( 'Edit <span class="screen-reader-text">%s</span>', 'my-custom-theme' ),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            get_the_title()
        ),
        '<span class="edit-link">',
        '</span>'
    );
}

/**
 * 为 WordPress 编辑器中的代码块自动添加 class
 */
function my_custom_theme_add_pre_classes( $block_content, $block ) {
    // 检查是否是代码块
    if ( 'core/code' === $block['blockName'] ) {
        // 为 pre 标签添加 line-numbers 类和其他必要的 class
        $block_content = preg_replace('/<pre/', '<pre class="line-numbers"', $block_content);
        
        // 确保 code 标签有正确的语言 class
        if ( isset( $block['attrs']['language'] ) && ! empty( $block['attrs']['language'] ) ) {
            $language = $block['attrs']['language'];
            // 替换或添加 language-xxx class
            $block_content = preg_replace('/<code\s+class="/', '<code class="language-' . $language . ' ', $block_content);
            // 如果 code 标签没有 class，直接添加
            $block_content = preg_replace('/<code(?!\s+class)/', '<code class="language-' . $language . '"', $block_content);
        } else {
            // 默认添加 language-text 类
            $block_content = preg_replace('/<code\s+class="/', '<code class="language-text ', $block_content);
            $block_content = preg_replace('/<code(?!\s+class)/', '<code class="language-text"', $block_content);
        }
    }
    
    return $block_content;
}

// 添加过滤器到 render_block
add_filter( 'render_block', 'my_custom_theme_add_pre_classes', 10, 2 );

/**
 * 在 WordPress 后台编辑器中添加 Prism.js 支持
 */
function my_custom_theme_enqueue_block_editor_assets() {
    // 只在编辑器中加载
    if ( is_admin() ) {
        // 加载 Prism.js CSS
        wp_enqueue_style(
            'prism-css',
            get_template_directory_uri() . '/css/prism.css',
            array(),
            '1.30.0'
        );
        
        // 加载行号插件 CSS
        wp_enqueue_style(
            'prism-line-numbers-css',
            get_template_directory_uri() . '/css/prism-line-numbers.css',
            array( 'prism-css' ),
            '1.30.0'
        );
        
        // 加载 Prism.js 核心
        wp_enqueue_script(
            'prism-core',
            get_template_directory_uri() . '/js/prism-core.js',
            array(),
            '1.30.0',
            true
        );
        
        // 加载行号插件
        wp_enqueue_script(
            'prism-line-numbers',
            get_template_directory_uri() . '/js/prism-line-numbers.js',
            array( 'prism-core' ),
            '1.30.0',
            true
        );
        
        // 加载自动加载器插件
        wp_enqueue_script(
            'prism-autoloader',
            get_template_directory_uri() . '/js/prism-autoloader.js',
            array( 'prism-core' ),
            '1.30.0',
            true
        );
    }
}

// 添加编辑器资源
add_action( 'enqueue_block_editor_assets', 'my_custom_theme_enqueue_block_editor_assets' );

/**
 * 在 WordPress 前台加载 Prism.js 支持
 */
function my_custom_theme_enqueue_frontend_assets() {
    // 加载 Prism.js CSS
    wp_enqueue_style(
        'prism-css',
        get_template_directory_uri() . '/css/prism.css',
        array(),
        '1.30.0'
    );
    
    // 加载行号插件 CSS
    wp_enqueue_style(
        'prism-line-numbers-css',
        get_template_directory_uri() . '/css/prism-line-numbers.css',
        array( 'prism-css' ),
        '1.30.0'
    );
    
    // 加载 Prism.js 核心
    wp_enqueue_script(
        'prism-core',
        get_template_directory_uri() . '/js/prism-core.js',
        array(),
        '1.30.0',
        true
    );
    
    // 加载行号插件
    wp_enqueue_script(
        'prism-line-numbers',
        get_template_directory_uri() . '/js/prism-line-numbers.js',
        array( 'prism-core' ),
        '1.30.0',
        true
    );
    
    // 加载自动加载器插件
    wp_enqueue_script(
        'prism-autoloader',
        get_template_directory_uri() . '/js/prism-autoloader.js',
        array( 'prism-core' ),
        '1.30.0',
        true
    );
    
    // 加载复制按钮插件
    wp_enqueue_script(
        'prism-copy-to-clipboard',
        get_template_directory_uri() . '/js/prism-copy-to-clipboard.js',
        array( 'prism-core' ),
        '1.30.0',
        true
    );
}

// 添加前台资源
add_action( 'wp_enqueue_scripts', 'my_custom_theme_enqueue_frontend_assets' );

/**
 * 添加自定义 JavaScript 来确保代码块正确显示
 */
function my_custom_theme_add_custom_js() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // 配置 Prism autoloader 使用本地语言组件路径
        if (Prism.plugins && Prism.plugins.autoloader) {
            // 设置本地语言组件路径
            Prism.plugins.autoloader.languages_path = '<?php echo get_template_directory_uri(); ?>/js/components/';
        }
        
        // 为所有代码块添加行号类和复制按钮
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
        
        // 确保所有代码都被高亮
        if (typeof Prism !== 'undefined') {
            Prism.highlightAll();
        }
    });
    </script>
    <?php
}

// 在前台添加自定义 JS
add_action( 'wp_footer', 'my_custom_theme_add_custom_js' );
