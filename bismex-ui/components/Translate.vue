<template>
  <div>
    <v-menu
      offset-y
      transition="slide-y-transition"
      min-width="200"
      max-height="250"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn
          color="#1C4F7C"
          dark
          v-bind="attrs"
          v-on="on"
          height="50"
          width="200"
        >
          <div class="t-flex t-items-center t-text-left">
            <div class="t-flex t-flex-row t-justify-center t-items-center">
              <v-avatar size="36" class="t-mr-2">
                <flag :iso="__selectedLangInfo.flag" style="font-size: 36px" />
              </v-avatar>
              <span style="font-size: 12px">{{ __selectedLangInfo.name }}</span>
            </div>
            <v-icon>mdi-chevron-down</v-icon>
          </div>
        </v-btn>
      </template>
      <v-list color="#1C4F7C">
        <v-list-item-group color="white">
          <v-list-item
            v-for="language in languages"
            :key="'lanuage-' + language.code"
            :data-lang-code="language.code"
            @click="translateHandler(language.code)"
          >
            <v-list-item-content>
              <div class="t-flex t-flex-row t-justify-start t-items-center">
                <v-avatar size="36" class="t-mr-2">
                  <flag :iso="language.flag" style="font-size: 36px" />
                </v-avatar>
                <span class="t-mt-1 t-text-white" style="font-size: 12px">
                  {{ language.name }}
                </span>
              </div>
            </v-list-item-content>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-menu>
    <div id="google_translate_element"></div>
  </div>
</template>

<script>

export default {
  props: {
    languages: {
      type: Array,
      default() {
        return [
          {
            code: "en",
            name: "English",
            cname: "英语",
            ename: "English",
            flag: "GB",
          },
          {
            code: "af",
            name: "Afrikaans",
            cname: "南非语",
            ename: "Afrikaans",
            flag: "AF",
          },
        ];
      },
    },
    defaultLanguageCode: {
      type: String,
      default: "en",
    },
    fetchBrowserLanguage: {
      type: Boolean,
      default: true,
    },
    animateTimeout: {
      type: Number,
      default: 150,
    },
    dropdownClassName: {
      type: String,
      default: "",
    },
    dropdownStyle: {
      type: Object,
      default: () => {
        return {};
      },
    },
    showArrow: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      countries: [],
      selected: null,
      selectedLanguageCode: "",
    };
  },
  computed: {
    value() {
      if (this.selected != null) {
        return this.selected;
      }

      return this.default;
    },
    __selectedLangInfo() {
      const selectedLanguageInfo = this.selectedLanguageInfo();
      return selectedLanguageInfo;
    },
  },
  methods: {
    translateHandler(code) {
      this.doGTranslate(code);
      this.selectedLanguageCode = code;
      this.$emit("select", this.selectedLanguageInfo());
      return false;
    },
    initUtils() {
      this.dynamicCreateStyle = (styles) => {
        const style = document.createElement("style");
        style.setAttribute("type", "text/css");
        style.innerHTML = styles;
        document.getElementsByTagName("head")[0].appendChild(style);
      };
      this.dynamicLoadJs = (jsUrl, fn, jsId = "") => {
        const _doc = document.querySelector("body");
        const script = document.createElement("script");
        script.setAttribute("type", "text/javascript");
        script.setAttribute("src", jsUrl);
        jsId && script.setAttribute("id", jsId);
        _doc.appendChild(script);
        script.onload = script.onreadystatechange = function () {
          if (
            !this.readyState ||
            this.readyState === "loaded" ||
            this.readyState === "complete"
          ) {
            fn && fn();
          }
          script.onload = script.onreadystatechange = null;
        };
      };
      this.getCookie = (name) => {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length >= 2) {
          return decodeURIComponent(parts.pop().split(";").shift());
        } else {
          return undefined;
        }
      };
      this.observer = (target, optionName, callback) => {
        if (!target) return;
        const MutationObserver =
          window.MutationObserver ||
          window.WebKitMutationObserver ||
          window.MozMutationObserver;
        const optionsMap = {
          attribute: {
            attribute: true,
            attributeOldValue: true,
          },
          child: {
            childList: true,
            subtree: true,
          },
        };
        if (MutationObserver) {
          const Observer = new MutationObserver((records) => {
            records.map((record) => {
              callback && callback(record);
            });
          });
          Observer.observe && Observer.observe(target, optionsMap[optionName]);
          return Observer;
        }
      };
    },
    initGoogleTranslate() {
      const _this = this;
      const createStyle = () => {
        this.dynamicCreateStyle(
          `body { top: 0 !important; } .skiptranslate { display: none !important; }`
        );
      };
      const createJsonCallback = () => {
        window.googleTranslateElementInit = function () {
          new window.google.translate.TranslateElement(
            { pageLanguage: "en", autoDisplay: false },
            "google_translate_element"
          );
          _this.setSelectedLanguageCode();
        };
      };
      const createScript = () => {
        this.dynamicLoadJs(
          "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit",
          () => {
            this.GTranslateFireEvent = (a, b) => {
              try {
                if (document.createEvent) {
                  const c = document.createEvent("HTMLEvents");
                  c.initEvent(b, true, true);
                  a.dispatchEvent(c);
                } else {
                  const c = document.createEventObject();
                  a.fireEvent("on" + b, c);
                }
              } catch (e) {
                console.warn(
                  `google transltate dispatch event has error: ${e}`
                );
              }
            };
            this.doGTranslate = (a) => {
              if (a.value) a = a.value;
              if (a === "") return;
              var b = a;
              var t = document.querySelector(".goog-te-combo");
              var gtel = document.querySelector(".eo__languages");
              if (
                gtel == null ||
                gtel.innerHTML.length === 0 ||
                t.options.length === 0
              ) {
                this.googleTranslateSelectObserver();
              } else {
                t.value = b;
                this.GTranslateFireEvent(t, "change");
                this._googleTranslateSelectObserver &&
                  this._googleTranslateSelectObserver.disconnect();
              }
            };
          }
        );
      };
      createStyle();
      createJsonCallback();
      createScript();
    },
    googleTranslateSelectObserver() {
      this._googleTranslateSelectObserver = this.observer(
        document.querySelector(".goog-te-combo"),
        "child",
        (record) => {
          if (record.addedNodes[0] && record.addedNodes[0].value) {
            if (this.selectedLanguageCode === record.addedNodes[0].value) {
              this.doGTranslate(record.addedNodes[0].value);
            }
          }
        }
      );
    },
    htmlLangObserver() {
      this._htmlLangObserver = this.observer(
        document.querySelector("html"),
        "attribute",
        (record) => {
          if (record.attributeName === "lang") {
            const currentValue = record.target.getAttribute("lang");
            const oldValue = record.oldValue;
            // 修复auto的中间状态，如果页面当中内容较多，gt会有一个翻译的过程，就会抛出lang = auto，此时我们手动再触发一次翻译覆盖掉上次未进行完的翻译操作
            if (
              oldValue !== currentValue &&
              oldValue &&
              oldValue !== "auto" &&
              currentValue === "auto"
            ) {
              this.translateHandler(this.selectedLanguageCode);
            }
          }
        }
      );
    },
    setSelectedLanguageCode() {
      const browserLanguage = this.fetchBrowserLanguage
        ? this.isLanguageCodeInLanguages(this.getBrowserLanguage())
        : "";
      const googleCookieLanguage = this.getGoogleCookieLanguage();
      const isFetchBrowserLanguageOpen = this.fetchBrowserLanguage;
      const isGoogleCookieLanguageExist = !!googleCookieLanguage;
      const handleDefaultLanguage = () => {
        if (this.defaultLanguageCode) {
          return this.defaultLanguageCode;
        } else {
          return "en";
        }
      };
      const handleBrowserLanguageInLanguages = () => {
        const isBrowserLanguageInLanguages = !!this.languages.find(
          (language) => language.code === browserLanguage
        );
        if (isBrowserLanguageInLanguages) {
          return browserLanguage;
        } else {
          return handleDefaultLanguage();
        }
      };
      const handleGoogleCookieLanguageInLanguages = () => {
        const isGoogleCookieLanguageInLanguages = !!this.languages.find(
          (language) => language.code === googleCookieLanguage
        );
        if (isGoogleCookieLanguageInLanguages) {
          return googleCookieLanguage;
        } else {
          return handleDefaultLanguage();
        }
      };
      let selectedCode = handleDefaultLanguage();
      if (!isGoogleCookieLanguageExist) {
        // 首次
        if (isFetchBrowserLanguageOpen)
          selectedCode = handleBrowserLanguageInLanguages();
      } else {
        // 非首次
        // 越过浏览器语言判断直接去列表中匹配
        selectedCode = handleGoogleCookieLanguageInLanguages();
      }
      this.translateHandler(selectedCode);
    },
    getBrowserLanguage() {
      const browserLanguage =
        navigator.language ||
        navigator.browserLanguage ||
        document.documentElement.lang ||
        "en";
      const filterLanguages = ["zh-CN", "zh-TW"];
      if (filterLanguages.every((l) => l !== browserLanguage)) {
        if (browserLanguage.indexOf("-") > -1) {
          return browserLanguage.split("-")[0];
        }
      }
      return browserLanguage;
    },
    getGoogleCookieLanguage() {
      const googleTranslateCookie = this.getCookie("googtrans");
      if (googleTranslateCookie) {
        const googleTranslateCookieResult = googleTranslateCookie.split("/");
        return googleTranslateCookieResult[2]
          ? googleTranslateCookieResult[2]
          : "en";
      } else {
        return "";
      }
    },
    isLanguageCodeInLanguages(code) {
      // 如果 code 不存在于后台配置的语言列表中默认使用英语
      const result = this.languages.find((language) => language.code === code);
      return result ? code : "en";
    },
    selectedLanguageInfo() {
      const target = this.languages.find(
        (language) => language.code === this.selectedLanguageCode
      );
      if (target) {
        return target;
      } else {
        const defaultTarget = this.languages.find(
          (language) => language.code === this.defaultLanguageCode
        );
        return defaultTarget;
      }
    },
  },
  created() {
    this.initUtils();
  },
  mounted() {
    this.countries = [
      {
        name: "English",
        value: "en",
        flag: "GB",
      },
      {
        name: "Vietnamese",
        value: "vi",
        flag: "VN",
      },
    ];

    if (this.acceptCountry != null) {
      this.countries = this.countries.filter((item) => {
        return this.acceptCountry.includes(item.value);
      });
    }

    if (this.exceptCountry != null) {
      this.countries = this.countries.filter((item) => {
        return !this.exceptCountry.includes(item.value);
      });
    }

    this.countries = _.uniqBy(this.countries, "value");
    this.initGoogleTranslate();
    this.htmlLangObserver();
  },
  beforeDestroy() {
    this._googleTranslateSelectObserver.disconnect();
    this._htmlLangObserver.disconnect();
  },
};
</script>
