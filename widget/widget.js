/*
 * Is it up? Widget
 * ================
 *
 *   Author: Sam Parkinson (@r3morse)
 * jsFiddle: http://jsfiddle.net/sparkinson/tspPU/
 *   GitHub: https://github.com/r3morse/isitup-widget
 *
 */

///
// Define our object and methods.
///
var isitup =
{
    // Our api host.
    server: "http://isitup.org/",

    // Domains we have made an API request for.
    requested: [],

    // The main function:
    // - Parses each widget parameters
    // - Inserts the empty widget html
    // - Makes the api requests
    run: function()
    {
        // An array of the widgets to update.
        var nodes = document.getElementsByClassName("isitup-widget");

        for (var i = 0; i < nodes.length; i++)
        {
            // Domain name from the widget.
            var domain = nodes[i].getAttribute("data-domain");

            // Initalise our html vaiable.
            var HTML = "";

            // Icon div.
            HTML += '<div class="isitup-icon">';
            HTML += '<img src="' + this.server + 'widget/img/loader.gif" width="16px" height="16px" style="vertical-align: middle;" />';
            HTML += '</div>';

            // Domain div.
            HTML += '<div class="isitup-domain">';
            HTML += '<a href="' + this.server + domain + '">' + domain + '</a>';
            HTML += '</div>';

            // Insert our widget html into its parent div.
            nodes[i].innerHTML = HTML;

            // Check the domain is valid.
            if (this.is_domain(domain))
            {
                // Insert our JSON request into the <head>.
                this.get_json(domain);
            }
            else // If the domain is invalid.
            {
                // Run update() with an invalid domain response locally.
                this.update({
                    "domain": domain,
                    "status_code": 3
                });
            }
        }

        return true;
    },

    // Function to inject our jsonp into the <head>.
    // @input domain              domain of the site to be checked
    get_json: function(domain)
    {
        // Check the json hasn't already been requested.
        if (!this.in_list(domain, this.requested))
        {
            var t = "script";

            var j = document.createElement(t),
                p = document.getElementsByTagName(t)[0];

            j.type = "text/javascript";

            j.src = this.server + domain + ".json?callback=isitup.update";

            p.parentNode.insertBefore(j, p);

            this.requested.push(domain);
        }
    },

    // Our callback function when the JSON response is downloaded.
    // - Finds the widget to update
    // - Updates the widgets image & link
    // @input result (json)       JSON object from the api response
    update: function(result)
    {
        // Update widget with the latest widget nodes.
        var nodes = document.getElementsByClassName("isitup-widget");

        // Go through the widgets and find the one we're updating.
        for (var i = 0; i < nodes.length; i++)
        {
            if (nodes[i].getAttribute("data-domain") === result.domain && !nodes[i].getAttribute("data-checked"))
            {
                // Look at the status code from the response.
                switch (result.status_code)
                {
                    // If the site is online.
                    case 1:
                        this.update_widget("online", "data-uplink", nodes[i]);

                        break;

                    // If it's offline.
                    case 2:
                        this.update_widget("offline", "data-downlink", nodes[i]);

                        break;

                    // If the domain is invalid.
                    case 3:
                        // Set the image to yellow.
                        this.set_image("error", nodes[i]);

                        // Set the link to http://isitup.org/d/<data-domain>
                        this.set_link(this.server + "d/" + nodes[i].getAttribute("data-domain"), nodes[i]);

                        break;
                }

                // Update it with the checked parameter.
                nodes[i].setAttribute("data-checked", true);
            }
        }
    },

    // Function to update the image and link of a given widget.
    update_widget: function(image, attribute, node)
    {
        // Change the icon.
        this.set_image(image, node);

        if (node.hasAttribute(attribute))
        {
        // Change the link to the user defined link.
            this.set_link(node.getAttribute(attribute), node);
        }
    },

    // Function to set the src parameter of a given <img> tag.
    // @input image               name of the image to insert
    // @input node                <img> node to insert the image into
    set_image: function(image, node)
    {
        node.getElementsByClassName("isitup-icon")[0]
            .firstChild
            .setAttribute("src", this.server + "widget/img/" + image + ".png");
    },

    // Function to set the href parameter of a given <a> tag
    // @input link                url to insert
    // @input node                <a> node to insert the url into
    set_link: function(link, node)
    {
        node.getElementsByClassName("isitup-domain")[0]
            .firstChild
            .setAttribute("href", link);
    },

    // A simple regex test for a domain.
    // @input domain              domain to test
    // @output boolean            true if domain is valid, otherwise false
    is_domain: function(domain)
    {
        re = /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/;

        return re.test(domain);
    },

    // Checks if a value is in a list.
    // @input value               value to check for
    // @input list                list to go through
    // @output boolean
    in_list: function(value, list)
    {
        for (var i = 0; i < list.length; i++)
        {
            if (list[i] === value) return true;
        }

        return false;
    },

    // Preloads the list of images used by the widget.
    load_images: function()
    {
        // The names of the images we want to preload.
        var images = ["online", "offline", "error"];

        // Create a new image object for preloading.
        var img = new Image(16,16);

        // Load each image to our object.
        for (var i = 0; i < images.length; i++)
        {
            img.src = this.server + "widget/img/" + images[i] + ".png";
        }
    }
};

///
// Run the widget!
///
(function()
{
    // Pre-load all our images.
    isitup.load_images();

    // Hook widget run method to the onload event.
    window.addEventListener("load", function() { isitup.run(); }, false);
}());